<?php

namespace App\Http\Controllers;

use App\Enum\LogModule;
use App\Http\Requests\Payment\GenerateLinkRequest;
use App\Http\Traits\BankCodeTrait;
use App\Http\Traits\RandomDigitTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Models\InvDetail;
use App\Models\InvoiceProgram;
use App\Models\Transaction;
use App\Services\Log\LogService;
use App\Services\Receipt\ReceiptService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Riskihajar\Terbilang\Facades\Terbilang;

class PaymentGatewayController extends Controller
{
    use BankCodeTrait, RandomDigitTrait, StandardizePhoneNumberTrait;

    protected $log_service;
    protected ClientProgramRepositoryInterface $clientProgramRepository;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected $admin_fee_va;
    protected $admin_fee_cc;

    public function __construct(
        LogService $log_service, 
        ClientProgramRepositoryInterface $clientProgramRepository,
        ReceiptRepositoryInterface $receiptRepository
        )
    {
        $this->log_service = $log_service;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->receiptRepository = $receiptRepository;
        $this->admin_fee_va = 4000;
        $this->admin_fee_cc = 2500; // not include 2.8%
    }

    public function redirectPayment(Request $request)
    {
        $payment_method = $request->route('payment_method');
        $bank = $request->get('bank');
        $installment = $request->get('installment');
        $id = $request->get('id');
        $response = Http::post(route('generate.payment.link', ['payment_method' => $payment_method]), [
            'bank' => $bank,
            'installment' => $installment,
            'id' => $id,
        ]);

        return $response->json();
    }

    public function generateLink(GenerateLinkRequest $request)
    {
        $validated = $request->safe()->only(['payment_method', 'bank', 'installment', 'id']);
        $payment_method = $validated['payment_method'];
        $bank_name = $validated['bank'] ?? null;
        $bank_id = $bank_name ? $this->getCodeBank($bank_name) : null;
        $installment = $validated['installment'];
        $identifier = $validated['id'];
        $trx_currency = 'IDR';

        //! need validation to prevent payment link generated twice if the bills has already paid

        $invoice_id = $invoice_dtl_id = null;
        if ( $installment == 1 )
        {
            $invoice = InvDetail::find($identifier);
            $invoice_dtl_id = $invoice->invdtl_id;
            $trx_amount = $invoice->invdtl_amountidr;
            $clientprog_id = $invoice->invoiceProgram->clientprog->clientprog_id;
            $product_name = $invoice->invoiceProgram->clientprog->invoice_program_name;
            $client = $invoice->invoiceProgram->clientprog->client;
            $remarks = $invoice->invoiceProgram->clientprog->invoice_program_name;
        } else {
            $invoice = InvoiceProgram::find($identifier);
            $invoice_id = $invoice->id;
            $trx_amount = $invoice->inv_totalprice_idr;
            $clientprog_id = $invoice->clientprog->clientprog_id;
            $product_name = $invoice->clientprog->program->program_name;
            $client = $invoice->clientprog->client;
            $remarks = $invoice->clientprog->invoice_program_name;
        }
        
        $va_fee = $payment_method == "VA" ? $this->admin_fee_va : $trx_amount*(2.8/100) + $this->admin_fee_cc;
        
        $invoice_number = $invoice->inv_id;
        $parent_number = $client->parents->count() > 0 ? $client->parents[0]->secondary_id : $client->secondary_id;
        $parent_name = $client->parents->count() > 0 ? $client->parents[0]->full_name : $client->full_name;
        $parent_email = $client->parents->count() > 0 ? $client->parents[0]->mail : $client->mail;
        $parent_phone = $client->parents->count() > 0 ? $client->parents[0]->phone : $client->phone;
        $parent_id = $client->parents->count() > 0 ? $client->parents[0]->id : $client->id;
        $parent_address = $client->parents->count() > 0 ? $client->parents[0]->address : $client->address;

        $trx_id = $this->tnRandomDigit();
        $merchant_ref_no = (string) $parent_number . $trx_id;

        # prevent transaction generated more than once by
        # checking the transaction table using invoice_id, installment_id, and invoice_number
        # if by those data transaction could be found, then use the transaction ID of existing data
        if ( $transaction = Transaction::where('invoice_id', $invoice_id)->where('installment_id', $invoice_dtl_id)->where('invoice_number', $invoice_number)->first() )
        {
            $trx_id = $transaction->trx_id;
            $merchant_ref_no = $transaction->merchant_ref_no;
        }

        $total_transaction_with_fee = $trx_amount + $va_fee;
        
        # create request body
        $request_body = [
            'merchant_key_id' => env('MERCHANT_KEY_ID'),
            'merchant_id' => env('MERCHANT_ID'),
            'merchant_ref_no' => $merchant_ref_no,
            'backend_callback_url' => env('PAYMENT_BACKEND_CALLBACK_URI'),
            'frontend_callback_url' => env('PAYMENT_FRONTEND_CALLBACK_URI'),
            'transaction_date_time' => Carbon::now()->format('Y-m-d H:i:s.v O'),
            'transmission_date_time' => Carbon::now()->format('Y-m-d H:i:s.v O'),
            'transaction_currency' => $trx_currency,
            'transaction_amount' => $total_transaction_with_fee,
            'product_details' => json_encode([[
                'item_code' => $trx_id,
                'item_title' => $product_name,
                'quantity' => 1,
                'total' => $trx_amount,
                'currency' => $trx_currency
            ]]),
            'va_name' => ucwords($parent_name),
            'user_name' => ucwords($parent_name),
            'user_email' => $parent_email,
            'user_phone_number' => $this->tnSetPhoneNumber($parent_phone),
            'user_id' => $parent_id,
            'remarks' => $remarks,
            'user_device_id' => Browser::browserName(),
            'user_ip_address' => $request->ip(),
            'shipping_details' => json_encode([
                'address' => $parent_address ?? "",
                'telephoneNumber' => $this->tnSetPhoneNumber($parent_phone),
                'handphoneNumber' => $this->tnSetPhoneNumber($parent_phone)
            ]),
            'invoice_number' => $invoice_number,
            'integration_type' => '01',
            'payment_method' => $payment_method,
            'bank_id' => $bank_id,
            'external_id' => (string) $trx_id,
            'other_bills' => json_encode([[
                'title' => 'admin fee',
                'value' => round($va_fee)
            ]])
        ];

        Log::debug('Request to Prismalink', $request_body);

        $response = Http::withHeaders([
            'mac' => hash_hmac('sha256', json_encode($request_body), env('PAYMENT_SECRET_KEY')),
        ])->
        post(env('PAYMENT_API_URI') . '/payment/integration/transaction/api/submit-trx', $request_body)->
        throw(function (Response $response, RequestException $err) use ($request_body) {
            $this->log_service->createErrorLog(LogModule::CREATE_PAYMENT_LINK, $err->getMessage(), $err->getLine(), $err->getFile(), $request_body);

            # in order to return error but display message to user
            # so we have to put the error into HTTP_OK
            # here's some condition only for duplicate transaction
            # other than that will using exception Error 
            if ( $err->getMessage() == 'INVALID_TRANSACTION_DUPLICATE' )
            {
                throw new HttpResponseException(
                    response()->json('Transaction has already been paid. Please refresh the page', JsonResponse::HTTP_OK)
                );    
            }

            throw new HttpResponseException(
                response()->json($err->getMessage(), JsonResponse::HTTP_BAD_REQUEST)
            );
        })->json();

        Log::debug('Response from Prismalink', $response);

        if ( $response['response_code'] != "PL000" )
        {
            throw new HttpResponseException(
                response()->json($response['response_message'], JsonResponse::HTTP_BAD_REQUEST)
            );
        }


        $trx_detail_to_store = [
            'trx_id' => $trx_id,
            'invoice_id' => $invoice_id,
            'installment_id' => $invoice_dtl_id,
            'invoice_number' => $invoice_number,
            'trx_currency' => $trx_currency,
            'trx_amount' => $trx_amount,
            'item_title' => $product_name,
            'payment_method' => $payment_method,
            'bank_id' => null,
            'bank_name' => null,
            'payment_page_url' => $response['payment_page_url'],
            'va_number' => null,
            'merchant_ref_no' => $merchant_ref_no,
            'plink_ref_no' => $response['plink_ref_no'],
            'validity' => Carbon::parse($response['validity']),
            'payment_status' => $response['transaction_status']
        ];

        if ($payment_method == "VA")
        {
            $va_number_list = json_decode($response['va_number_list'])[0];
            $trx_detail_to_store['bank_id'] = $va_number_list->bank_id;
            $trx_detail_to_store['bank_name'] = $va_number_list->bank;
            $trx_detail_to_store['va_number'] = $va_number_list->va;
        }
        

        DB::beginTransaction();
        try {    
            $trx = Transaction::updateOrCreate([
                'trx_id' => $trx_id,
                'merchant_ref_no' => $merchant_ref_no
            ], $trx_detail_to_store);
            DB::commit();
        } catch (Exception $err) {
            DB::rollBack();
            $this->log_service->createErrorLog(LogModule::CREATE_PAYMENT_LINK, $err->getMessage(), $err->getLine(), $err->getFile(), $trx_detail_to_store);
            return response()->json([
                'response_description' => 'ERR',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->log_service->createSuccessLog(LogModule::CREATE_PAYMENT_LINK, 'Payment link created successfully', $trx->toArray());
        return response()->json([
            'response_description' => 'SUCCESS',
            'payment_link' => env('PAYMENT_WEB_URI').$response['payment_page_url']
        ]);
    }

    public function callback(
        Request $request,
        ReceiptService $receipt_service,
        LogService $log_service,
        )
    {
        Log::debug('Callback triggered by Prismalink', $request->all());
        $payment_ref_no = $request->payment_ref_no;
        $merchant_ref_no = $request->merchant_ref_no;
        $payment_status = $request->payment_status;

        DB::beginTransaction();
        try {
            # update transaction status
            $transaction = Transaction::where('merchant_ref_no', $merchant_ref_no)->firstOrFail();
            $transaction->payment_status = $payment_status;
            $transaction->save();

            # get client_prog eloquent
            $invoice_model = $transaction->invoice_id === null ? InvDetail::find($transaction->installment_id) : InvoiceProgram::find($transaction->invoice_id);
            $client_prog_model = $transaction->invoice_id === null ? $invoice_model->invoiceProgram->clientprog : $invoice_model->clientprog;
            $client_prog = $this->clientProgramRepository->getClientProgramById($client_prog_model->clientprog_id);

            # if payment is SETLD 
            # it has to trigger to generate receipt as well
            if ( $payment_status == "SETLD" )
            {
                # store in Log if the client has paid more than it should be
                if ( $request->transaction_amount != $transaction->trx_amount )
                    Log::warning("Please double check the transaction no. ". $transaction->trx_id);

                $invoice_type = $transaction->invoice_id != NULL && $transaction->installment_id ? "Program" : "Installment";
                $identifier = $transaction->invoice_id != NULL && $transaction->installment_id ? $invoice_model->inv_id : $transaction->installment_id;
                if ( $this->receiptRepository->getReceiptByInvoiceIdentifier($invoice_type, $identifier) )
                {
                    Log::warning("Transaction no. {$transaction->trx_id} had been triggered but already has receipt" );
                    return response()->json([
                        'message' => 'Payment received'
                    ]);
                }

                $transaction_amount = $request->transaction_amount;
                if ( $transaction->payment_method == "VA" )
                    $transaction_amount -= $this->admin_fee_va;
                else
                    $transaction_amount -= $transaction->trx_amount*(2.8/100) + $this->admin_fee_cc;

                $is_child_program_bundle = $client_prog->bundlingDetail()->count();
                $receipt_details = [
                    'receipt_id' => $receipt_service->generateReceiptId(['receipt_date' => $request->payment_date], $client_prog, $is_child_program_bundle),
                    'inv_id' => $invoice_model->inv_id,
                    'invdtl_id' => $transaction->installment_id,
                    'rec_currency' => 'IDR', # by default it would be IDR
                    'receipt_amount' => null,
                    'receipt_amount_idr' => $transaction_amount,
                    'receipt_date' => $request->payment_date,
                    'receipt_words' => null,
                    'receipt_words_idr' => ucfirst(str_replace(',' ,'', Terbilang::make($transaction_amount))) . ' Rupiah',
                    'receipt_method' => $request->payment_method,
                    'receipt_cheque' => null,
                    'receipt_cat' => 'student', # by default it would be student
                    'created_at' => $request->payment_date,
                ];

                $receipt_created = $this->receiptRepository->createReceipt($receipt_details);
            }
            DB::commit();
        } catch (Exception $err) {
            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_RECEIPT_PROGRAM_FROM_PAYMENT_GA, $err->getMessage(), $err->getLine(), $err->getFile(), $request->all());
            return response()->json([
                'message' => 'There\'s a  problem when receiving payment'
            ]);
        }

        $this->log_service->createSuccessLog(LogModule::STORE_RECEIPT_PROGRAM_FROM_PAYMENT_GA, 'Receipt created successfully', $receipt_created->toArray());
        return response()->json([
            'message' => 'Payment received'
        ]);
    }
}

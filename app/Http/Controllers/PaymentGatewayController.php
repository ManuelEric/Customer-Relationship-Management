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

    public function __construct(
        LogService $log_service, 
        ClientProgramRepositoryInterface $clientProgramRepository,
        ReceiptRepositoryInterface $receiptRepository
        )
    {
        $this->log_service = $log_service;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->receiptRepository = $receiptRepository;
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
        $va_fee = $payment_method == "VA" ? 4000 : 0;
        $bank_name = $validated['bank'];
        $bank_id = $this->getCodeBank($bank_name);
        $installment = $validated['installment'];
        $identifier = $validated['id'];
        $trx_currency = 'IDR';

        # need validation to prevent payment link generated twice

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
        
        $invoice_number = $invoice->inv_id;
        $parent_number = $client->parents->count() > 0 ? $client->parents[0]->secondary_id : $client->secondary_id;
        $parent_name = $client->parents->count() > 0 ? $client->parents[0]->full_name : $client->full_name;
        $parent_email = $client->parents->count() > 0 ? $client->parents[0]->mail : $client->mail;
        $parent_phone = $client->parents->count() > 0 ? $client->parents[0]->phone : $client->phone;
        $parent_id = $client->parents->count() > 0 ? $client->parents[0]->id : $client->id;
        $parent_address = $client->parents->count() > 0 ? $client->parents[0]->address : $client->address;

        $trx_id = $this->tnRandomDigit();
        $merchant_ref_no = (string) $parent_number . $trx_id;
        
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
            'transaction_amount' => $trx_amount + $va_fee,
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
            'external_id' => (string) $trx_id
        ];

        if ($payment_method == "VA")
        {
            $request_body['other_bills'] = json_encode([[
                'title' => 'admin fee',
                'value' => $va_fee,
            ]]);
        }

        $response = Http::withHeaders([
            'mac' => hash_hmac('sha256', json_encode($request_body), env('PAYMENT_SECRET_KEY')),
        ])->
        post(env('PAYMENT_API_URI') . '/payment/integration/transaction/api/submit-trx', $request_body)->
        throw(function (Response $response, RequestException $err) use ($request_body) {
            $this->log_service->createErrorLog(LogModule::CREATE_PAYMENT_LINK, $err->getMessage(), $err->getLine(), $err->getFile(), $request_body);
        })->json();

        if ( $response['response_code'] != "PL000" )
            throw new Exception($response['response_message']);

        $va_number_list = json_decode($response['va_number_list'])[0];
    
        $trx_detail_to_store = [
            'trx_id' => $trx_id,
            'invoice_id' => $invoice_id,
            'installment_id' => $invoice_dtl_id,
            'invoice_number' => $invoice_number,
            'trx_currency' => $trx_currency,
            'trx_amount' => $trx_amount,
            'item_title' => $product_name,
            'payment_method' => $payment_method,
            'bank_id' => $va_number_list->bank_id,
            'bank_name' => $va_number_list->bank,
            'payment_page_url' => $response['payment_page_url'],
            'va_number' => $va_number_list->va,
            'merchant_ref_no' => $merchant_ref_no,
            'plink_ref_no' => $response['plink_ref_no'],
            'validity' => Carbon::parse($response['validity']),
            'payment_status' => $response['transaction_status']
        ];

        DB::beginTransaction();
        try {    
            $trx = Transaction::create($trx_detail_to_store);
            DB::commit();
        } catch (Exception $err) {
            DB::rollBack();
            $this->log_service->createErrorLog(LogModule::CREATE_PAYMENT_LINK, $err->getMessage(), $err->getLine(), $err->getFile(), $trx_detail_to_store);
            return response()->json([
                'response_description' => 'ERR',
            ]);
        }

        $this->log_service->createSuccessLog(LogModule::CREATE_PAYMENT_LINK, 'Payment link created successfully', $trx->toArray());
        return response()->json([
            'response_description' => 'SUCCESS'
        ]);
    }

    public function callback(
        Request $request,
        ReceiptService $receipt_service,
        LogService $log_service,
        )
    {
        $payment_ref_no = $request->payment_ref_no;
        $merchant_ref_no = $request->merchant_ref_no;
        $payment_status = $request->payment_status;

        DB::beginTransaction();
        try {
            # update transaction status
            $transaction = Transaction::where('merchant_ref_no', $merchant_ref_no)->first();
            $transaction->payment_status = $payment_status;
            $transaction->save();

            # get client_prog eloquent
            $invoice_model = $transaction->invoice_id === null ? InvDetail::query() : InvoiceProgram::query();
            $client_prog_model = $transaction->invoice_id === null ? $invoice_model->invoiceProgram->clientprog : $invoice_model->clientprog;
            $client_prog = $this->clientProgramRepository->getClientProgramById($client_prog_model->clientprog_id);

            # if payment is SETLD 
            # it has to trigger to generate receipt as well
            if ( $payment_status == "SETLD" )
            {
                $is_child_program_bundle = $client_prog->bundlingDetail()->count();
                $receipt_details = [
                    'receipt_id' => $receipt_service->generateReceiptId(['receipt_date' => $request->payment_date], $client_prog, $is_child_program_bundle),
                    'rec_currency' => 'IDR', # by default it would be IDR
                    'receipt_amount' => null,
                    'receipt_amount_idr' => $request->transaction_amount,
                    'receipt_date' => $request->payment_date,
                    'receipt_words' => null,
                    'receipt_words_idr' => Terbilang::make($request->transaction_amount) . ' rupiah only',
                    'receipt_method' => $request->payment_method,
                    'receipt_cheque' => null,
                    'receipt_cat' => 'student', # by default it would be student
                    'created_at' => $request->payment_date,
                ];
                Log::debug('receipt details', $receipt_details);

                $receipt_created = $this->receiptRepository->createReceipt($receipt_details);
            }
            DB::commit();
        } catch (Exception $err) {
            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_RECEIPT_PROGRAM_FROM_PAYMENT_GA, $err->getMessage(), $err->getLine(), $err->getFile(), $request->all());
            return false;
        }

        $this->log_service->createSuccessLog(LogModule::STORE_RECEIPT_PROGRAM_FROM_PAYMENT_GA, 'Receipt created successfully', $receipt_created->toArray());
        return true;
    }
}

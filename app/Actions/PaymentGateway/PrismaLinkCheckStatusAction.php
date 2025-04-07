<?php

namespace App\Actions\PaymentGateway;

use App\Enum\LogModule;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Models\ClientProgram;
use App\Models\InvoiceProgram;
use App\Models\Transaction;
use App\Services\Log\LogService;
use App\Services\Receipt\ReceiptService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Riskihajar\Terbilang\Facades\Terbilang;

class PrismaLinkCheckStatusAction
{
    protected $log_service;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected $receipt_service;
    protected $admin_fee_va;
    protected $admin_fee_cc;

    public function __construct(LogService $log_service, ReceiptRepositoryInterface $receiptRepository, ReceiptService $receipt_service)
    {
        $this->log_service = $log_service;
        $this->receiptRepository = $receiptRepository;
        $this->receipt_service = $receipt_service;
        $this->admin_fee_va = 4000;
        $this->admin_fee_cc = 2500; // not include 2.8%
    }

    public function execute(array $request)
    {
        $additional_data = []; # only for return values, can be used for anything outside the response data
        $request_body = [
            'plink_ref_no' => $request['plink_ref_no'],
            'merchant_key_id' => env('MERCHANT_KEY_ID'),
            'transmission_date_time' => Carbon::now()->format('Y-m-d H:i:s.v O'),
            'merchant_ref_no' => $request['merchant_ref_no'],
            'merchant_id' => env('MERCHANT_ID')
        ];

        Log::debug('Request Check Status', $request_body);

        $response = Http::withHeaders([
            'mac' => hash_hmac('sha256', json_encode($request_body), env('PAYMENT_SECRET_KEY')),
        ])->
        post(env('PAYMENT_API_URI') . '/payment/integration/transaction/api/inquiry-transaction', $request_body)->
        throw(function (Response $response, RequestException $err) use ($request_body) {
            $this->log_service->createErrorLog(LogModule::CHECK_PAYMENT_STATUS, $err->getMessage(), $err->getLine(), $err->getFile(), $request_body);
            throw new Exception(
                response()->json($err->getMessage(), JsonResponse::HTTP_BAD_REQUEST)
            );
        })->json();

        Log::debug('Response of checking status', $response);

        # if the response from prisma link was not PL000
        # it means that there is a problem within the process
        if ( $response['response_code'] != "PL000" )
        {
            Log::debug('Error while requesting check status' . $response['response_message']);
            throw new Exception(
                response()->json($response['response_message'], JsonResponse::HTTP_BAD_REQUEST)
            );
        }

        switch ($response['transaction_status'])
        {
            case "SETLD":
                # update the transaction status
                $transaction = Transaction::where('plink_ref_no', $request_body['plink_ref_no'])->first();
                $transaction->payment_status = $response['transaction_status'];
                $transaction->save();

                $invoice_type = $transaction->invoice_id != NULL && $transaction->installment_id ? "Program" : "Installment";
                $identifier = $transaction->invoice_id != NULL && $transaction->installment_id ? $transaction->invoice_number : $transaction->installment_id;

                # check if the transaction has already made a receipt or not?
                if ( $this->receiptRepository->getReceiptByInvoiceIdentifier($invoice_type, $identifier) )
                {
                    Log::warning("Transaction no. {$transaction->trx_id} had been checked but already has receipt" );
                    $message = 'Check status complete';
                }
                else
                {
                    # if payment status inside transaction is still PNDNG 
                    # and somehow callback doesn't got triggered
                    # so the solution is with this check status
                    # if the response return SETLD and don't have a receipt yet
                    # here's the code to create one

                    $transaction_amount = $response['transaction_amount'];
                    if ( $transaction->payment_method == "VA" )
                        $transaction_amount -= $this->admin_fee_va;
                    else
                        $transaction_amount -= $transaction->trx_amount*(2.8/100) + $this->admin_fee_cc;

                    $crm_invoice = InvoiceProgram::where('inv_id', $transaction->invoice_number)->first();
                    $crm_client_program = $crm_invoice->clientprog;
                    $is_child_program_bundle = $crm_client_program->bundlingDetail()->count();

                    $receipt_details = [
                        'receipt_id' => $this->receipt_service->generateReceiptId(['receipt_date' => $response['payment_date']], $crm_client_program, $is_child_program_bundle),
                        'inv_id' => $transaction->invoice_number,
                        'invdtl_id' => $transaction->installment_id,
                        'rec_currency' => 'IDR', # by default it would be IDR
                        'receipt_amount' => null,
                        'receipt_amount_idr' => $transaction_amount,
                        'receipt_date' => $response['payment_date'],
                        'receipt_words' => null,
                        'receipt_words_idr' => ucfirst(str_replace(',' ,'', Terbilang::make($transaction_amount))) . ' Rupiah',
                        'receipt_method' => $response['payment_method'],
                        'receipt_cheque' => null,
                        'receipt_cat' => 'student', # by default it would be student
                        'created_at' => $response['payment_date'],
                    ];

                    $receipt_created = $this->receiptRepository->createReceipt($receipt_details);
                    $this->log_service->createSuccessLog(LogModule::STORE_RECEIPT_PROGRAM_FROM_PAYMENT_GA, 'Receipt created successfully', $receipt_created->toArray());
                    $additional_data = $receipt_created;
                    $message = "Check status complete and successfully create receipt";
                }
                break;
        }

        return [$response, $additional_data, $message];
    }
}
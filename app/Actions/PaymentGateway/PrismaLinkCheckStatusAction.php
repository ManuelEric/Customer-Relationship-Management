<?php

namespace App\Actions\PaymentGateway;

use App\Enum\LogModule;
use App\Models\Transaction;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class PrismaLinkCheckStatusAction
{
    protected $log_service;

    public function __construct(LogService $log_service)
    {
        $this->log_service = $log_service;
    }

    public function execute(array $transaction)
    {
        $request_body = [
            'plink_ref_no' => $transaction['plink_ref_no'],
            'merchant_key_id' => env('MERCHANT_KEY_ID'),
            'transmission_date_time' => Carbon::now()->format('Y-m-d H:i:s.v O'),
            'merchant_ref_no' => $transaction['merchant_ref_no'],
            'merchant_id' => env('MERCHANT_ID')
        ];

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

        if ( $response['response_code'] != "PL000" )
        {
            throw new Exception(
                response()->json($response['response_message'], JsonResponse::HTTP_BAD_REQUEST)
            );
        }

        # update the payment status
        $transaction = Transaction::where('plink_ref_no', $request_body['plink_ref_no'])->first();

        # check if payment status is SETLD, is it does have a receipt?
        # it supposed to have a Receipt
        if ( $transaction->payment_status == 'SETLD' )
        {
            
        }
    }
}
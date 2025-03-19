<?php

namespace App\Http\Controllers;

use App\Http\Traits\BankCodeTrait;
use App\Models\InvDetail;
use App\Models\InvoiceProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PaymentGatewayController extends Controller
{
    use BankCodeTrait;

    public function generateLink(Request $request)
    {
        $bank = $this->getCodeBank($request->get('bank'));
        $installment = $request->get('installment');
        $identifier = $request->get('id');

        if ( $installment == 1 )
        {
            $invoice = InvDetail::find($identifier);
        } else {
            InvoiceProgram::find($identifier);
        }
        
        # create request body
        $request_body = [
            'merchant_key_id' => env('MERCHANT_KEY_ID'),
            'merchant_id' => env('MERCHANT_ID'),
            'merchant_ref_no' => $invoice->inv_id,
            'backend_callback_url' => '',
            'frontend_callback_url' => '',
            'transaction_date_time' => Carbon::now(),
            'transmission_date_time' => Carbon::now(),
            'transaction_currency' => 'IDR',
            'transaction_amount' => 
        ];
    }
}

<?php

namespace App\Http\Traits\PaymentGateway;

trait CalculateFeeTrait
{
    public function calculateFee(string $payment_method, $bank_va_fee, $trx_amount)
    {
        switch ($payment_method)
        {
            case "VA":
                $fee = $bank_va_fee;
                break;

            case "CC":
                // with credit card, they charged 1.5% + 1.500 per transaction
                $fee = $trx_amount*(2.5/100) + 1500;
                break;

            case "QR":
                // with qris, they charged 0.7% per transaction
                $fee = $trx_amount*(0.7/100);
                break;
        }
        
        return $fee;
    }
}
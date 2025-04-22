<?php

namespace App\Http\Traits\PaymentGateway;

trait CalculateFeeTrait
{
    public function calculateFee(string $payment_method, $trx_amount)
    {
        switch ($payment_method)
        {
            case "VA":
                $fee = 4000;
                break;

            case "CC":
                // with credit card, they charged 2.8% + 2.500 per transaction
                $fee = $trx_amount*(2.8/100) + 2500;
                break;

            case "QR":
                // with qris, they charged 0.7% per transaction
                $fee = $trx_amount*(0.7/100);
                break;
        }
        
        return $fee;
    }
}
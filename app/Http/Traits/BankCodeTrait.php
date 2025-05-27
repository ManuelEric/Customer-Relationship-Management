<?php

namespace App\Http\Traits;

trait BankCodeTrait
{
    public function getCodeBank(string $bank_name)
    {
        switch (strtolower($bank_name))
        {
            case "bca":
                $code_bank = '014';
                $fee = 3750;
                break;

            case "bri":
                $code_bank = "002";
                $fee = 3500;
                break;

            case "niaga":
                $code_bank = "022";
                $fee = 2500;
                break;

            case "mandiri":
                $code_bank = "008";
                $fee = 3500;
                break;
        }

        return [$code_bank, $fee];

    }
}
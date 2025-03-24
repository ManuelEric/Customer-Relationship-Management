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
                break;

            case "bri":
                $code_bank = "002";
                break;

            case "niaga":
                $code_bank = "022";
                break;

            case "mandiri":
                $code_bank = "008";
                break;
        }

        return $code_bank;

    }
}
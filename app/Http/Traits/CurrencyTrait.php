<?php

namespace App\Http\Traits;

trait CurrencyTrait
{

    public function formatCurrency(string $currency, int $subunit_idr, int $subunit_other)
    {
        switch ($currency) {

            case "idr":
                $locale = 'id_ID';
                $symbol = "Rp";
                $formatter = number_format($subunit_idr);
                break;

            case "usd":
                $locale = 'en_US';
                $symbol = "$";
                $formatter = number_format($subunit_other, 2);
                break;

            case "sgd":
                $locale = 'en_SG';
                $symbol = "S$";
                $formatter = number_format($subunit_other, 2);
                break;

            case "gbp":
                $locale = 'en_DE';
                $symbol = "£";
                $formatter = number_format($subunit_other, 2);
                break;

        }

        return $symbol.' '.$formatter;
    }
}

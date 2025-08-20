<?php

namespace App\Http\Traits;

trait RupiahFormatterTrait
{
    public function formatRupiah($amount)
    {
        // Format the amount as a string with thousands separator and currency symbol
        return 'Rp. '.number_format($amount, 0, ',', '.');
    }
}

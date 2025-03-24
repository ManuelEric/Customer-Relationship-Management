<?php

namespace App\Http\Traits;

use App\Models\Transaction;

trait RandomDigitTrait
{
    public function tnRandomDigit()
    {
        do {
            $randomize = mt_rand(0000000000, 9999999999);
        } while ( Transaction::where('trx_id', $randomize)->exists() );
        
        return $randomize;
    }
}
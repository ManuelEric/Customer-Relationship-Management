<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyRateController extends Controller
{
    public function getCurrencyRate(Request $request)
    {
        $base_currency = strtoupper($request->base_currency);
        $to_currency = $request->to_currency;

        # get currency rate per today
        $response = Http::withUrlParameters([
            'apiKey' => 'q7gj9pblo8gc6smvididunjibs6bpf8q42rc65g2a8t0lgq2skcu8',
            'base_currency' => $base_currency,
            'to_currency' => $to_currency,
            'amount' => '1',
        ])->get('https://anyapi.io/api/v1/exchange/convert?apiKey={apiKey}&base={base_currency}&to={to_currency}&amount={amount}');
        $todayRate = round($response['rate']);

        return response()->json(['rate' => $todayRate]);
    }
}

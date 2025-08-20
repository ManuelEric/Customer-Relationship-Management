<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DataClient implements WithMultipleSheets
{
    public function sheets(): array
    {

        $sheets = [
            new DataNewClient('New Client'),
            new DataPotentialClient('Potential Client'),
        ];

        return $sheets;
    }
}

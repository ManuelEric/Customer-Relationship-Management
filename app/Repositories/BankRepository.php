<?php

namespace App\Repositories;

use App\Interfaces\BankRepositoryInterface;
use App\Models\Bank;

class BankRepository implements BankRepositoryInterface
{
    public function getBanks()
    {
        return Bank::orderBy('bank_name', 'asc')->get();
    }

}

<?php

namespace App\Repositories;

use App\Interfaces\VendorTypeRepositoryInterface;
use App\Models\VendorType;

class VendorTypeRepository implements VendorTypeRepositoryInterface 
{
    public function getAllVendorType()
    {
        return VendorType::orderBy('name', 'asc')->get();
    }
}
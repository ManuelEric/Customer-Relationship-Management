<?php

namespace App\Repositories;

use App\Interfaces\IndustryRepositoryInterface;
use App\Models\Industry;

class IndustryRepository implements IndustryRepositoryInterface 
{

    public function rnGetAllIndustries()
    {
        return Industry::all();
    }
}
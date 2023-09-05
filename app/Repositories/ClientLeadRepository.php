<?php

namespace App\Repositories;

use App\Interfaces\ClientLeadRepositoryInterface;
use App\Models\ViewClientLead;

class ClientLeadRepository implements ClientLeadRepositoryInterface 
{
    public function getAllClientLeads()
    {
        return ViewClientLead::with('leadStatus')->where('grade', '!=', null)->where('grade', '<=', 0)->get();
    }
}
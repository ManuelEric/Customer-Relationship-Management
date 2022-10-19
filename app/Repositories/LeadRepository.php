<?php

namespace App\Repositories;

use App\Interfaces\LeadRepositoryInterface;
use App\Models\Lead;
use DataTables;

class LeadRepository implements LeadRepositoryInterface 
{
    public function getAllLeadDataTables()
    {
        return Datatables::eloquent(Lead::query())->make(true);
    }

    public function getAllLead()
    {
        return Lead::orderBy('main_lead', 'asc')->orderBy('sub_lead', 'asc')->get();
    }

    public function getLeadById($leadId)
    {
        return Lead::whereLeadId($leadId);
    }

    public function deleteLead($leadId)
    {
        // return Lead::destroy($leadId);
        return Lead::whereLeadId($leadId)->delete();
    }

    public function createLead(array $leadDetails)
    {
        return Lead::create($leadDetails);
    }

    public function updateLead($leadId, array $newDetails)
    {
        return Lead::whereLeadId($leadId)->update($newDetails);
    }
}
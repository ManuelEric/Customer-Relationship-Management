<?php

namespace App\Repositories;

use App\Interfaces\LeadRepositoryInterface;
use App\Models\Lead;
use App\Models\v1\Lead as V1Lead;
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

    public function getAllMainLead()
    {
        return Lead::where('sub_lead', NULL)->orderBy('main_lead', 'asc')->get();
    }

    public function getAllKOLlead()
    {
        return Lead::where('main_lead', 'KOL')->orderBy('sub_lead', 'asc')->get();
    }

    public function getLeadById($leadId)
    {
        return Lead::whereLeadId($leadId);
    }
    public function getLeadByMainLead($main_lead)
    {
        return Lead::where('main_lead', $main_lead)->first();
    }

    public function getLeadByName($leadName)
    {
        return Lead::whereLeadName($leadName);
    }

    public function deleteLead($leadId)
    {
        // return Lead::destroy($leadId);
        return Lead::whereLeadId($leadId)->delete();
    }

    public function createLeads(array $leadDetails)
    {
        return Lead::insert($leadDetails);
    }

    public function createLead(array $leadDetails)
    {
        return Lead::create($leadDetails);
    }

    public function updateLead($leadId, array $newDetails)
    {
        return Lead::whereLeadId($leadId)->update($newDetails);
    }

    # from lead big data v1 model
    public function getAllLeadFromV1()
    {
        return V1Lead::where('lead_id', '!=', '')->orderBy('lead_id', 'asc')->select(['lead_id', 'lead_name as main_lead'])->get();
    }
}

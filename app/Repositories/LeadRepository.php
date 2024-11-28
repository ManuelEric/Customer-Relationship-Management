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
        $query = Lead::leftJoin('tbl_department', 'tbl_department.id', '=', 'tbl_lead.department_id')->select('tbl_lead.*', 'tbl_department.dept_name');
        return Datatables::eloquent($query)->
                    filterColumn('dept_name', function ($query, $keyword) {
                        $query->whereRaw('tbl_department.dept_name like ?', ["%{$keyword}%"]);
                    })->
                    addIndexColumn()->
                    make(true);
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

    public function getActiveLead()
    {
        return Lead::where('status', 1)->get();
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
        return tap(Lead::whereLeadId($leadId))->update($newDetails);
    }

    # from lead big data v1 model
    public function getAllLeadFromV1()
    {
        return V1Lead::where('lead_id', '!=', '')->orderBy('lead_id', 'asc')->select(['lead_id', 'lead_name as main_lead'])->get();
    }

    public function getLeadForFormEmbedEvent()
    {
        // Get all lead without All-in Partners and All-in Event
        return Lead::where('lead_id', '!=', 'LS003')->where('lead_id', '!=', 'LS010')->where('status', 1)->get();
    }
}

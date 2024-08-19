<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use Illuminate\Http\Request;

class ExtLeadController extends Controller
{
    protected LeadRepositoryInterface $leadRepository;
    protected EdufLeadRepositoryInterface $edufLeadRepository;

    public function __construct(LeadRepositoryInterface $leadRepository, EdufLeadRepositoryInterface $edufLeadRepository)
    {
        $this->leadRepository = $leadRepository;
        $this->edufLeadRepository = $edufLeadRepository;
    }

    public function getLeadSources(Request $request)
    {
        $edufLeads = $this->edufLeadRepository->getEdufairLeadByYear(date('Y'));

        $leads = $this->leadRepository->getActiveLead();
        if (!$leads) {
            return response()->json([
                'success' => true,
                'message' => 'No lead found.'
            ]);
        }

        # map the data that being shown to the user
        $mappedLeads = $leads->map(function ($value) use ($edufLeads) {
            $lead_id = $value->lead_id;
            $lead_name = $value->lead_name;
            $id = $value->id;

            if($value->lead_id != 'LS017') # External Edufair
            {
                return [
                    'lead' => $lead_name,
                    'id' => $id,
                    'id_raw' => $id, # for ordering data
                    'lead_id' => $lead_id,
                    'department' => $value->department_name
                ];
            }

        });

        # get lead where main lead is edufair
        # 'LS017' is lead id edufair
        $mainEdufLead = $leads->where('lead_id', 'LS017')->first();

        # looping list of edufair
        # push edufair list to collection mapped leads
        foreach ($edufLeads as $edufLead) {
            
            $lead_id = $mainEdufLead->lead_id . '-' . $edufLead->id;
            $lead_name = $mainEdufLead->lead_name . ' : ' . $edufLead->organizer_name;
            $id = $mainEdufLead->id . '-' . $edufLead->id;

            $additionalLeads = [
                'lead' => $lead_name,
                'id' => $id,
                'id_raw' => $mainEdufLead->id, # for ordering data
                'lead_id' => $lead_id,
                'department' => $mainEdufLead->department_name
            ];

            $mappedLeads->push($additionalLeads);
        }
      
        return response()->json([
            'success' => true,
            'message' => 'There are leads found.',
            'data' => $mappedLeads->sortBy('id_raw')->values()
        ]);
    }
}

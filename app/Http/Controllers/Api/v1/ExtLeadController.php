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
        $eduf_leads = $this->edufLeadRepository->getEdufairLeadByYear(date('Y'));

        $leads = $this->leadRepository->getActiveLead();
        if (!$leads) {
            return response()->json([
                'success' => true,
                'message' => 'No lead found.'
            ]);
        }

        # map the data that being shown to the user
        $mapped_leads = $leads->map(function ($value) {
            $lead_id = $value->lead_id;
            $lead_name = $value->lead_name;
            $id = $value->id;

            return [
                'lead' => $lead_name,
                'id' => $id,
                'id_raw' => $id, # for ordering data
                'lead_id' => $lead_id,
                'department' => $value->department_name
            ];

        });

        # filter mapped lead where lead id != LS017 (EduFair)
        $filtered = $mapped_leads->filter(function ($value, $key) {
            return $value['lead_id']!='LS017';
        });
        

        # get lead where main lead is edufair
        # 'LS017' is lead id edufair
        $main_eduf_lead = $leads->where('lead_id', 'LS017')->first();
    
        # looping list of edufair
        # push edufair list to collection mapped leads
        foreach ($eduf_leads as $eduf_lead) {
            
            $lead_id = $main_eduf_lead->lead_id . '-' . $eduf_lead->id;
            $lead_name = $main_eduf_lead->lead_name . ' : ' . $eduf_lead->organizer_name;
            $id = $main_eduf_lead->id . '-' . $eduf_lead->id;

            $additional_leads = [
                'lead' => $lead_name,
                'id' => $id,
                'id_raw' => $main_eduf_lead->id, # for ordering data
                'lead_id' => $lead_id,
                'department' => $main_eduf_lead->department_name
            ];

            $filtered->push($additional_leads);
        }
      
        return response()->json([
            'success' => true,
            'message' => 'There are leads found.',
            'data' => $filtered->sortBy('id_raw')->values()
        ]);
    }
}

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

        // $leads = $this->leadRepository->getAllLead();
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

            if($value->lead_id == 'LS017') # External Edufair
            {
                foreach ($edufLeads as $edufLead) {
                    $lead_id = $value->lead_id . '-' . $edufLead->id;
                    $lead_name = $value->lead_name . ' : ' . $edufLead->title;
                    $id = $value->id . '-' . $edufLead->id;
                }
            }

            return [
                'lead' => $lead_name,
                'id' => $id,
                'lead_id' => $lead_id,
                'department' => $value->department_name
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'There are leads found.',
            'data' => $mappedLeads
        ]);
    }
}

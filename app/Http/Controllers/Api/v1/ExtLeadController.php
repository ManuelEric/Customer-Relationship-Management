<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\LeadRepositoryInterface;
use Illuminate\Http\Request;

class ExtLeadController extends Controller
{
    protected LeadRepositoryInterface $leadRepository;

    public function __construct(LeadRepositoryInterface $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function getLeadSources(Request $request)
    {
        $leads = $this->leadRepository->getAllLead();
        if (!$leads) {
            return response()->json([
                'success' => true,
                'message' => 'No lead found.'
            ]);
        }

        # map the data that being shown to the user
        $mappedLeads = $leads->map(function ($value) {
            return [
                'lead' => $value->lead_name,
                'id' => $value->id,
                'lead_id' => $value->lead_id,
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

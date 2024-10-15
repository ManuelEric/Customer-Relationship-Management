<?php

namespace App\Actions\Leads;

use App\Http\Requests\StoreLeadRequest;
use App\Interfaces\LeadRepositoryInterface;
use App\Services\Master\LeadService;

class UpdateLeadAction
{
    private LeadRepositoryInterface $leadRepository;
    private LeadService $leadService;

    public function __construct(LeadRepositoryInterface $leadRepository, LeadService $leadService)
    {
        $this->leadRepository = $leadRepository;
        $this->leadService = $leadService;
    }

    public function execute(
        StoreLeadRequest $request,
        String $lead_id,
        Array $new_lead_details
    )
    {

        $new_lead_details = $this->leadService->snSetMainLeadAndSubLead($request);

        # Update lead
        $updated_lead = $this->leadRepository->updateLead($lead_id, $new_lead_details);

        return $updated_lead;
    }
}
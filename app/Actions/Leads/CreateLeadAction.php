<?php

namespace App\Actions\Leads;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\LeadRepositoryInterface;
use App\Models\Asset;
use App\Models\Lead;
use App\Services\Master\LeadService;

class CreateLeadAction
{
    use CreateCustomPrimaryKeyTrait;
    private LeadRepositoryInterface $leadRepository;
    private LeadService $leadService;

    public function __construct(LeadRepositoryInterface $leadRepository, LeadService $leadService)
    {
        $this->leadRepository = $leadRepository;
        $this->leadService = $leadService;
    }

    public function execute(
        StoreLeadRequest $request,
        Array $new_lead_details
    )
    {

        $last_id = Lead::max('lead_id');
        if (!$last_id)
            $last_id = 'LS000';

        $new_lead_details = $this->leadService->snSetMainLeadAndSubLead($request);

        $lead_id_without_label = $this->remove_primarykey_label($last_id, 2);
        $lead_id_with_label = 'LS' . $this->add_digit($lead_id_without_label + 1, 3);

        # store new lead
        $new_data_lead = $this->leadRepository->createLead(['lead_id' => $lead_id_with_label] + $new_lead_details);

        return $new_data_lead;
    }
}
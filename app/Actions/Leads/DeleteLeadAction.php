<?php

namespace App\Actions\Leads;

use App\Interfaces\LeadRepositoryInterface;

class DeleteLeadAction
{
    private LeadRepositoryInterface $leadRepository;

    public function __construct(LeadRepositoryInterface $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function execute(
        String $lead_id
    )
    {
        # Update curriculum
        $lead =  $this->leadRepository->deleteLead($lead_id);

        return $lead;
    }
}
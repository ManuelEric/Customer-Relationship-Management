<?php

namespace App\Actions\EdufLeads;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\EdufLeadRepositoryInterface;

class DeleteEdufLeadAction
{
    use CreateCustomPrimaryKeyTrait;
    private EdufLeadRepositoryInterface $edufLeadRepository;

    public function __construct(EdufLeadRepositoryInterface $edufLeadRepository)
    {
        $this->edufLeadRepository = $edufLeadRepository;
    }

    public function execute(
        String $eduf_lead_id
    )
    {
        # Delete eduf_lead
        $deleted_eduf_lead = $this->edufLeadRepository->deleteEdufairLead($eduf_lead_id);

        return $deleted_eduf_lead;
    }
}
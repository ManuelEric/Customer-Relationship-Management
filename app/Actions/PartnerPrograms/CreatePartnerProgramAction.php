<?php

namespace App\Actions\PartnerPrograms;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Services\Master\ReasonService;

class CreatePartnerProgramAction
{
    use CreateCustomPrimaryKeyTrait;
    private PartnerProgramRepositoryInterface $partnerProgramRepository;
    private ReasonService $reasonService;

    public function __construct(PartnerProgramRepositoryInterface $partnerProgramRepository, ReasonService $reasonService)
    {
        $this->partnerProgramRepository = $partnerProgramRepository;
        $this->reasonService = $reasonService;
    }

    public function execute(
        $corp_id,
        $parnter_program_details,
    )
    {

        $parnter_program_details['corp_id'] = $corp_id;

        # Set and create reason when user select other reason
        $parnter_program_details = $this->reasonService->snSetAndCreateReasonProgram($parnter_program_details);

        # store new partner program
        $new_data_partner_program = $this->partnerProgramRepository->createPartnerProgram($parnter_program_details);

        return $new_data_partner_program;
    }
}
<?php

namespace App\Actions\PartnerPrograms;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Services\Master\ReasonService;
use Carbon\Carbon;

class UpdatePartnerProgramAction
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
        $partner_prog_id,
        $corp_id,
        $parnter_program_details,
    )
    {

        $parnter_program_details['corp_id'] = $corp_id;
        $parnter_program_details['updated_at'] = Carbon::now();

        # Set and create reason when user select other reason
        $parnter_program_details = $this->reasonService->snSetAndCreateReasonProgram($parnter_program_details);

        # update partner program
        $updated_partner_program = $this->partnerProgramRepository->updatePartnerProgram($partner_prog_id, $parnter_program_details);

        return $updated_partner_program;
    }
}
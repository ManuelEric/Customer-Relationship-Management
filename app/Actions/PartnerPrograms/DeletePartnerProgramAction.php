<?php

namespace App\Actions\PartnerPrograms;

use App\Interfaces\PartnerProgramRepositoryInterface;

class DeletePartnerProgramAction
{
    private PartnerProgramRepositoryInterface $partnerProgramRepository;

    public function __construct(PartnerProgramRepositoryInterface $partnerProgramRepository)
    {
        $this->partnerProgramRepository = $partnerProgramRepository;
    }

    public function execute(
        $partner_prog_id,
    )
    {

        # deleted partner program
        $deleted_partner_program = $this->partnerProgramRepository->deletePartnerProgram($partner_prog_id);


        return $deleted_partner_program;
    }
}
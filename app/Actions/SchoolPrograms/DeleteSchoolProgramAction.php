<?php

namespace App\Actions\SchoolPrograms;

use App\Interfaces\SchoolProgramRepositoryInterface;

class DeleteSchoolProgramAction
{
    private SchoolProgramRepositoryInterface $schoolProgramRepository;

    public function __construct(SchoolProgramRepositoryInterface $schoolProgramRepository)
    {
        $this->schoolProgramRepository = $schoolProgramRepository;
    }

    public function execute(
        $sch_prog_id,
    )
    {

        # deleted partner program
        $deleted_partner_program = $this->schoolProgramRepository->deleteSchoolProgram($sch_prog_id);


        return $deleted_partner_program;
    }
}
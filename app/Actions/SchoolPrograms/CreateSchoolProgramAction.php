<?php

namespace App\Actions\SchoolPrograms;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Services\Master\ReasonService;

class CreateSchoolProgramAction
{
    use CreateCustomPrimaryKeyTrait;
    private SchoolProgramRepositoryInterface $schoolProgramRepository;
    private ReasonService $reasonService;

    public function __construct(SchoolProgramRepositoryInterface $schoolProgramRepository, ReasonService $reasonService)
    {
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->reasonService = $reasonService;
    }

    public function execute(
        String $school_id,
        Array $school_program_details,
    )
    {

        $school_program_details['sch_id'] = $school_id;

        # Set and create reason when user select other reason
        $school_program_details = $this->reasonService->snSetAndCreateReasonProgram($school_program_details);
        
        # store new school program
        $new_data_school_program = $this->schoolProgramRepository->createSchoolProgram($school_program_details);
       
        return $new_data_school_program;
    }
}
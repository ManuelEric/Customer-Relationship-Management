<?php

namespace App\Actions\SchoolPrograms;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Services\Master\ReasonService;
use Carbon\Carbon;

class UpdateSchoolProgramAction
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
        string $school_id,
        $sch_prog_id,
        Array $school_program_details,
    )
    {

        $school_program_details['sch_id'] = $school_id;
        $school_program_details['updated_at'] = Carbon::now();

        
        # Set and create reason when user select other reason
        $school_program_details = $this->reasonService->snSetAndCreateReasonProgram($school_program_details);

        # update school program
        $updated_school_program = $this->schoolProgramRepository->updateSchoolProgram($sch_prog_id, $school_program_details);

        return $updated_school_program;
    }
}
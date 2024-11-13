<?php

namespace App\Actions\Schools;

use App\Http\Requests\StoreSchoolRequest;
use App\Interfaces\SchoolCurriculumRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;

class UpdateSchoolAction
{
    private SchoolRepositoryInterface $schoolRepository;
    private SchoolCurriculumRepositoryInterface $schoolCurriculumRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, SchoolCurriculumRepositoryInterface $schoolCurriculumRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->schoolCurriculumRepository = $schoolCurriculumRepository;
    }

    public function execute(
        StoreSchoolRequest $request,
        String $school_id,
        Array $school_details
    )
    {
        # update school
        $updated_school = $this->schoolRepository->updateSchool($school_id, $school_details);

        # update sch curriculum
        $new_school_curriculum_details = $request->sch_curriculum;
       
        $this->schoolCurriculumRepository->updateSchoolCurriculum($school_id, $new_school_curriculum_details);
       
        return $updated_school;
    }
}
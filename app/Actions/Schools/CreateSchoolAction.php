<?php

namespace App\Actions\Schools;

use App\Http\Requests\StoreSchoolRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\SchoolCurriculumRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Models\School;

class CreateSchoolAction
{
    use CreateCustomPrimaryKeyTrait;
    private SchoolRepositoryInterface $schoolRepository;
    private SchoolCurriculumRepositoryInterface $schoolCurriculumRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, SchoolCurriculumRepositoryInterface $schoolCurriculumRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->schoolCurriculumRepository = $schoolCurriculumRepository;
    }

    public function execute(
        StoreSchoolRequest $request,
        Array $school_details
    )
    {
        $last_id = School::max('sch_id');
        $school_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 4) : '0000';
        $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

        # store new school
        $new_school = $this->schoolRepository->createSchool(['sch_id' => $school_id_with_label] + $school_details);

        # insert into sch curriculum
        $schoolCurriculumDetails = $request->sch_curriculum;

        $this->schoolCurriculumRepository->createSchoolCurriculum($school_id_with_label, $schoolCurriculumDetails);
        
        return $new_school;
    }
}
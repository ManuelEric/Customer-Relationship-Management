<?php

namespace App\Repositories;

use App\Interfaces\SchoolCurriculumRepositoryInterface;
use App\Models\School;

class SchoolCurriculumRepository implements SchoolCurriculumRepositoryInterface 
{
    public function getAllCurriculumBySchoolId($schoolId)
    {
        $school = School::whereSchoolId($schoolId);
        return $school->curriculum;
    }

    public function createSchoolCurriculum($schoolId, array $schoolCurriculumDetails)
    {
        $school = School::whereSchoolId($schoolId);
        $school->curriculum()->attach($schoolCurriculumDetails);
        return $school;
    }

    public function updateSchoolCurriculum($schoolId, array $newCurriculumDetails)
    {   
        $school = School::whereSchoolId($schoolId);
        $school->curriculum()->sync($newCurriculumDetails);
        return $school;
    }
}
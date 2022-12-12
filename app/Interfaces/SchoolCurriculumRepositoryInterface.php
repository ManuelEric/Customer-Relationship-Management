<?php

namespace App\Interfaces;

interface SchoolCurriculumRepositoryInterface 
{
    public function getAllCurriculumBySchoolId($schoolId);
    public function createSchoolCurriculum($schoolId, array $schoolCurriculumDetails);
    public function updateSchoolCurriculum($schoolId, array $newCurriculumDetails);
}
<?php

namespace App\Interfaces;

interface SchoolProgramRepositoryInterface
{

    public function getAllSchoolProgramsById($schoolId);
    public function getSchoolProgramById($schoolProgramId);
    public function deleteSchoolProgram($schoolProgramId);
    public function createSchoolProgram(array $schoolPrograms);
    public function updateSchoolProgram($schoolProgramId, array $schoolPrograms);
}

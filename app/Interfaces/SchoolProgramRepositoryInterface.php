<?php

namespace App\Interfaces;

interface SchoolProgramRepositoryInterface
{
    public function getAllSchoolProgramsDataTables(array $filter);
    public function getAllSchoolProgramsBySchoolId($schoolId);
    public function getSchoolProgramById($schoolProgramId);
    public function deleteSchoolProgram($schoolProgramId);
    public function createSchoolProgram(array $schoolPrograms);
    public function updateSchoolProgram($schoolProgramId, array $schoolPrograms);
    public function getReportSchoolPrograms($start_date, $end_date);
}

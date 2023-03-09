<?php

namespace App\Interfaces;

interface SchoolProgramRepositoryInterface
{
    public function getAllSchoolProgramsDataTables(array $filter);
    public function getAllSchoolProgramsBySchoolId($schoolId);
    public function getAllSchoolProgramByStatusAndMonth($status, $monthYear);
    public function getStatusSchoolProgramByMonthly($monthYear);
    public function getSchoolProgramById($schoolProgramId);
    public function deleteSchoolProgram($schoolProgramId);
    public function createSchoolProgram(array $schoolPrograms);
    public function createSchoolPrograms(array $schoolPrograms);
    public function updateSchoolProgram($schoolProgramId, array $schoolPrograms);
    public function getReportSchoolPrograms($start_date, $end_date);
    public function getTotalSchoolProgramComparison($startYear, $endYear);
    public function getSchoolProgramComparison($startYear, $endYear);
    public function getAllSchoolProgramFromCRM();
}

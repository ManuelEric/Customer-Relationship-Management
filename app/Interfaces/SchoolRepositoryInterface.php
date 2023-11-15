<?php

namespace App\Interfaces;

interface SchoolRepositoryInterface
{
    public function getAllSchoolDataTables();
    public function getAllSchools();
    public function getSchoolByMonthly($monthYear, $type);
    public function getSchoolById($schoolId);
    public function getSchoolByName($schoolName);
    public function deleteSchool($schoolId);
    public function createSchool(array $schoolDetails);
    public function createSchoolIfNotExists(array $schoolDetails, array $schoolCurriculum);
    public function findSchoolByTerms($searchTerms);
    public function attachCurriculum($schoolId, array $curriculums);
    public function createSchools(array $schoolDetails);
    public function updateSchool($schoolId, array $schoolDetails);
    public function cleaningSchool();
    public function cleaningSchoolDetail();
    public function getReportNewSchool($start_date, $end_date);
    public function getFeederSchools($eventId);
    public function getUncompeteSchools();

    public function getAllSchoolFromV1();
}

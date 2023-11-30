<?php

namespace App\Interfaces;

interface SchoolRepositoryInterface
{
    public function getAllSchoolDataTables($isRaw = false);
    public function getAllSchools();
    public function getSchoolByMonthly($monthYear, $type);
    public function getSchoolById($schoolId);
    public function getSchoolByName($schoolName);
    public function getSchoolByAlias($alias);
    public function getAliasBySchool($schoolId);
    public function getDuplicateSchools();
    public function getDuplicateUnverifiedSchools();
    public function getUnverifiedSchools();

    public function deleteSchool($schoolId);
    public function moveToTrash($schoolId);
    public function moveBulkToTrash(array $schoolIds);
    public function createSchool(array $schoolDetails);
    public function createSchoolIfNotExists(array $schoolDetails, array $schoolCurriculum);
    public function findSchoolByTerms($searchTerms);
    public function findUnverifiedSchool($schoolId);
    public function findVerifiedSchool($schoolId);
    public function attachCurriculum($schoolId, array $curriculums);
    public function createSchools(array $schoolDetails);
    public function updateSchool($schoolId, array $schoolDetails);
    public function updateSchools(array $schoolIds, array $newDetails);
    public function cleaningSchool();
    public function cleaningSchoolDetail();
    public function getReportNewSchool($start_date, $end_date);
    public function getFeederSchools($eventId);
    public function getUncompeteSchools();

    # CRM v1
    public function getAllSchoolFromV1();

    # alias
    public function createNewAlias(array $aliasDetail);
    public function deleteAlias($aliasid);
}

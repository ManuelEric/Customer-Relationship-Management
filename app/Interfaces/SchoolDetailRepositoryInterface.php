<?php

namespace App\Interfaces;

interface SchoolDetailRepositoryInterface
{
    public function getAllSchoolDetailDataTables($schoolId);
    public function getAllSchoolDetailsById($schoolId);
    public function getSchoolDetailById($schoolDetailId);
    public function deleteAgendaSpeaker($schoolId, $eventId);
    public function getAllSchoolDetails();
    public function deleteSchoolDetail($schoolDetailId);
    public function createSchoolDetail(array $schoolDetails);
    public function updateSchoolDetail($schoolDetailId, array $schoolDetails);
    public function getAllSchoolDetailFromCRM();
}

<?php

namespace App\Interfaces;

interface SchoolVisitRepositoryInterface
{
    public function getSchoolVisitById($visitId);
    public function getSchoolVisitBySchoolId($schoolId);
    public function createSchoolVisit(array $visitDetails);
    public function updateSchoolVisit($visitId, array $newDetails);
    public function deleteSchoolVisit($visitId);
    public function getReportSchoolVisit($start_date, $end_date);
}

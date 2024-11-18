<?php

namespace App\Interfaces;

interface ReferralRepositoryInterface
{
    public function getAllReferralDataTables();
    public function getAllReferralByTypeAndMonth($type, $monthYear);
    public function getReferralTypeByMonthly($monthYear);
    public function getReferralById($referralId);
    public function createReferral(array $referralDetails);
    public function updateReferral($referralId, array $newDetails);
    public function deleteReferral($referralId);
    public function getReferralComparison($startYear, $endYear);
    public function getTotalReferralProgramComparison($startYear, $endYear);
    public function getReportNewReferral($start_date, $end_date, $type);
    public function getReferralByCorpIdAndDate($corpId, $refDate);
    public function getReferralFromV1();
}

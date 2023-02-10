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
    public function getReferralComparisonStart($startYear);
    public function getReferralComparisonEnd($endYear);
    public function getReferralComparison($startYear, $endYear);
}

<?php

namespace App\Interfaces;

interface ReferralRepositoryInterface 
{
    public function getAllReferralDataTables();
    public function getReferralById($referralId);
    public function createReferral(array $referralDetails);
    public function updateReferral($referralId, array $newDetails);
    public function deleteReferral($referralId);
}
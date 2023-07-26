<?php

namespace App\Interfaces;

interface FollowupRepositoryInterface 
{
    public function getAllFollowupByClientProgramId($clientProgramId);
    public function getAllFollowupScheduleByDate($requested_date);
    public function createFollowup(array $followupDetails);
    public function updateFollowup($followupId, array $newDetails);
    public function deleteFollowup($followupId);

    # dashboard
    public function getAllFollowupWithin($days, $month = null);
}
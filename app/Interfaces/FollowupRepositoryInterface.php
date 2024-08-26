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

    public function getScheduledAppointmentsByUser(array $advanced_filter);
    public function getFollowedUpAppointmentsByUser(array $advanced_filter);

    # followup client
    public function getAllFollowupClientScheduleByDate($requested_date);
    public function findFollowupClient($followupId);
    public function create(array $followupDetails);
    public function update($followupId, array $followupDetails);
    public function getAllFollowupClientWithin(int $days);
}
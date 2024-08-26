<?php

namespace App\Interfaces;

interface VolunteerRepositoryInterface
{
    public function getAllVolunteerDataTables();
    public function getAllVolunteer();
    public function getVolunteerById($volunteerId);
    public function deleteVolunteer($volunteerId);
    public function createVolunteer(array $volunteerDetails);
    public function updateVolunteer($volunteerId, array $newDetails);
    public function updateActiveStatus($volunteerId, $newStatus);
    public function cleaningVolunteer();
}

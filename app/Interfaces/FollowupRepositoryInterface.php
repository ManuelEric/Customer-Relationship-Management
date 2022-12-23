<?php

namespace App\Interfaces;

interface FollowupRepositoryInterface 
{
    public function getAllFollowupByClientProgramId($clientProgramId);
    public function createFollowup(array $followupDetails);
    public function deleteFollowup($followupId);
}
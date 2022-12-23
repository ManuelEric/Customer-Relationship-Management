<?php

namespace App\Repositories;

use App\Interfaces\FollowupRepositoryInterface;
use App\Models\FollowUp;

class FollowupRepository implements FollowupRepositoryInterface 
{
    public function getAllFollowupByClientProgramId($clientProgramId)
    {
        return FollowUp::where('clientprog_id', $clientProgramId)->get();
    }

    public function createFollowup(array $followupDetails)
    {   
        return FollowUp::create($followupDetails);
    }

    public function deleteFollowup($followupId)
    {
        return FollowUp::destroy($followupId);
    }
}
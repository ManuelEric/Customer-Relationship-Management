<?php

namespace App\Repositories;

use App\Interfaces\FollowupRepositoryInterface;
use App\Models\FollowUp;
use DateTime;
use Illuminate\Support\Carbon;

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

    public function updateFollowup($followupId, array $newDetails)
    {
        return FollowUp::whereId($followupId)->update($newDetails);
    }

    public function deleteFollowup($followupId)
    {
        return FollowUp::destroy($followupId);
    }

    # dashboard
    # getting follow up data 
    # within next 7 days
    public function getAllFollowupWithin($days, $filter = null)
    {
        $today = date('Y-m-d');
        $lastday = date('Y-m-d', strtotime('+'.$days.' days'));

        $data = [];
        if ($followup = FollowUp::when($filter, function($query) use ($filter) {
            $query->whereMonth('followup_date', date('m', strtotime($filter)))->whereYear('followup_date', date('Y', strtotime($filter)));
        }, function ($query) use ($today, $lastday) {
            $query->whereBetween('followup_date', [$today, $lastday]);
        })->orderBy('followup_date', 'asc')->get()) {

            foreach ($followup as $detail) 
            {
                $data[$detail->followup_date][] = $detail;
            }

        }

        return $data;
        

    }
}
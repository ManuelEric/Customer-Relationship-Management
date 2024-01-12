<?php

namespace App\Repositories;

use App\Interfaces\FollowupRepositoryInterface;
use App\Models\FollowUp;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class FollowupRepository implements FollowupRepositoryInterface 
{
    public function getAllFollowupByClientProgramId($clientProgramId)
    {
        return FollowUp::where('clientprog_id', $clientProgramId)->get();
    }

    public function getAllFollowupScheduleByDate($requested_date)
    {   
        return FollowUp::where('followup_date', $requested_date)->where('reminder', 0)->get();
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
    public function getAllFollowupWithin($days, $month = null)
    {
        $today = date('Y-m-d');
        $lastday = date('Y-m-d', strtotime('+'.$days.' days'));

        $data = [];

        # employee that logged in
        $empl_id = auth()->guard('api')->user()->id ?? auth()->user()->id;

        $followup = FollowUp::when($month, function($query) use ($month) {
                        $query->whereMonth('followup_date', date('m', strtotime($month)))->whereYear('followup_date', date('Y', strtotime($month)));
                    }, function ($query) use ($today, $lastday) {
                        $query->whereBetween('followup_date', [$today, $lastday]);
                    })->
                    when(Session::get('user_role') == 'Employee', function ($query) use ($empl_id) {
                        $query->whereHas('clientProgram', function ($subQuery) use ($empl_id) {
                            $subQuery->where(function ($Q2) use ($empl_id) {
                                
                                $Q2->where('empl_id', $empl_id)->orWhereHas('viewClient', function ($Q3) use ($empl_id) {
                                    $Q3->where('pic_id', $empl_id);
                                });
                            });
                        });
                    })->
                    when(auth()->guard('api')->user(), function ($query) use ($empl_id) {
                        $query->whereHas('clientProgram', function ($subQuery) use ($empl_id) {
                            $subQuery->where(function ($Q2) use ($empl_id) {
                                
                                $Q2->where('empl_id', $empl_id)->orWhereHas('viewClient', function ($Q3) use ($empl_id) {
                                    $Q3->where('pic_id', $empl_id);
                                });
                            });
                        });
                    })->
                    orderBy('followup_date', 'asc')->get();

        if ($followup) {

            foreach ($followup as $detail) 
            {
                $data[$detail->followup_date][] = $detail;
            }

        }

        return $data;
        

    }
}
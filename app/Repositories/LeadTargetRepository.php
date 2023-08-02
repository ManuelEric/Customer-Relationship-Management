<?php

namespace App\Repositories;

use App\Interfaces\LeadTargetRepositoryInterface;
use App\Models\LeadTargetTracking;
use App\Models\UserClient;
use App\Models\ViewClientProgram;
use App\Models\ViewTargetSignal;

class LeadTargetRepository implements LeadTargetRepositoryInterface
{
    public function getThisMonthTarget()
    {
        return ViewTargetSignal::all();
    }

    public function findThisMonthTarget($now)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        return LeadTargetTracking::whereMonth('month_year', $month)->whereYear('month_year', $year)->get();
    }

    public function getIncompleteTargetFromLastMonthByDivision($now, $divisi)
    {
        $last_month = date('m', strtotime('-1 month', strtotime($now)));
        $last_year = date('Y', strtotime('-1 month', strtotime($now)));
        
        return LeadTargetTracking::whereMonth('month_year', $last_month)->whereYear('month_year', $last_year)->where('divisi', $divisi)->first();
    }

    public function getAchievedLeadSalesByMonth($current_month)
    {
        return UserClient::
                    whereHas('lead', function ($query) {
                        $query->where('note', 'Sales')->where('main_lead', '!=', 'Referral');    
                    })->
                    whereMonth('pivot.updated_at', $current_month)->
                    groupBy('pivot.client_id')->
                    get();
    }
}

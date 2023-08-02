<?php

namespace App\Repositories;

use App\Interfaces\TargetTrackingRepositoryInterface;
use App\Models\TargetTracking;
use Illuminate\Support\Facades\DB;

class TargetTrackingRepository implements TargetTrackingRepositoryInterface 
{
    public function getTargetTrackingMonthlyByDivisi($monthYear, $divisi)
    {
        return TargetTracking::whereMonth('month_year', date('m', strtotime($monthYear)))
                                ->whereYear('month_year', date('Y', strtotime($monthYear)))
                                ->where('divisi', $divisi)
                                ->first();
    }

    public function getTargetTrackingPeriod($startDate, $endDate)
    {
        return TargetTracking::select(DB::raw('SUM(target_lead + target_hotleads + target_initconsult + contribution_target) as target'), DB::raw('SUM(achieved_lead + achieved_hotleads + achieved_initconsult + contribution_achieved) as actual'), DB::raw('Month(month_year) as month'))
                            ->whereBetween('month_year', [$startDate, $endDate])->groupBy(DB::raw('Month(month_year)'))->get();
    }

}
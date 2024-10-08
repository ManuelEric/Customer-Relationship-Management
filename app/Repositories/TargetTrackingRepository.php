<?php

namespace App\Repositories;

use App\Interfaces\TargetTrackingRepositoryInterface;
use App\Models\TargetTracking;
use Illuminate\Support\Facades\DB;

class TargetTrackingRepository implements TargetTrackingRepositoryInterface 
{
    public function getAllTargetTrackingMonthly($monthYear)
    {
        return TargetTracking::whereMonth('month_year', date('m', strtotime($monthYear)))
                                ->whereYear('month_year', date('Y', strtotime($monthYear)))
                                ->get();
    }

    public function getTargetTrackingMonthlyByDivisi($monthYear, $divisi)
    {
        return TargetTracking::whereMonth('month_year', date('m', strtotime($monthYear)))
                                ->whereYear('month_year', date('Y', strtotime($monthYear)))
                                ->where('divisi', $divisi)
                                ->first();
    }

    public function getTargetTrackingPeriod($startDate, $endDate, $type)
    {
        switch ($type) {
            case 'lead':
                $query = TargetTracking::select(DB::raw('SUM(contribution_target) as target'), DB::raw('SUM(contribution_achieved) as actual'), DB::raw('DATE_FORMAT(month_year, "%Y-%m") as month_year'))
                           ->groupBy(DB::raw('Year(month_year)'), DB::raw('Month(month_year)'))->get();
                break;
            case 'revenue':
                $query = TargetTracking::select('revenue_target as target', 'revenue_achieved as actual', DB::raw('DATE_FORMAT(month_year, "%Y-%m") as month_year'))
                        ->groupBy(DB::raw('Year(month_year)'), DB::raw('Month(month_year)'))->get();
                break;
        }
        
        
        return $query;
    }
}
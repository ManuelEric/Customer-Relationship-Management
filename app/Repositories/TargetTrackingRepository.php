<?php

namespace App\Repositories;

use App\Interfaces\TargetTrackingRepositoryInterface;
use App\Models\TargetTracking;
use Illuminate\Support\Facades\DB;

class TargetTrackingRepository implements TargetTrackingRepositoryInterface 
{
    public function getTargetTrackingByMonthYear($monthYear)
    {
        return TargetTracking::whereMonth('month', date('m', strtotime($monthYear)))
                                ->whereYear('year', date('Y', strtotime($monthYear)))
                                ->get();
    }

}
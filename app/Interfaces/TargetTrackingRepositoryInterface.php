<?php

namespace App\Interfaces;

interface TargetTrackingRepositoryInterface 
{
    public function getTargetTrackingMonthlyByDivisi($monthYear, $divisi);
    public function getTargetTrackingPeriod($startDate, $endDate);
}
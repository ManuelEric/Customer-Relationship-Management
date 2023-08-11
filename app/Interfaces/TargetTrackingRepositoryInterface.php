<?php

namespace App\Interfaces;

interface TargetTrackingRepositoryInterface 
{
    public function getAllTargetTrackingMonthly($monthYear);
    public function getTargetTrackingMonthlyByDivisi($monthYear, $divisi);
    public function getTargetTrackingPeriod($startDate, $endDate, $type);
}
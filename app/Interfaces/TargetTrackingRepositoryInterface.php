<?php

namespace App\Interfaces;

interface TargetTrackingRepositoryInterface 
{
    public function getTargetTrackingByMonthYear($monthYear);

}
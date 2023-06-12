<?php

namespace App\Interfaces;

interface AcadTutorRepositoryInterface 
{
    public function getAllScheduleAcadTutorH1Day();
    public function getAllScheduleAcadTutorT3Hours();
    public function markAsSent($sentDetail);
}

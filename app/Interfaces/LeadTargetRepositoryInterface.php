<?php

namespace App\Interfaces;

interface LeadTargetRepositoryInterface 
{
    public function getThisMonthTarget();
    public function findThisMonthTarget($now);
    public function getIncompleteTargetFromLastMonthByDivision($now, $divisi);
}

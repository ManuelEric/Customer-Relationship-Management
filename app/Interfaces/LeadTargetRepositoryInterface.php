<?php

namespace App\Interfaces;

interface LeadTargetRepositoryInterface 
{
    public function getThisMonthTarget();
    public function findThisMonthTarget($current_month);
    public function getIncompleteTargetFromLastMonthByDivision($current_month, $divisi);

    public function getAchievedLeadSalesByMonth($current_month);
}

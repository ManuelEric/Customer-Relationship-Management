<?php

namespace App\Interfaces;

interface LeadTargetRepositoryInterface 
{
    public function getThisMonthTarget();
    public function findThisMonthTarget($now);
    public function findThisMonthTargetByDivision($now, $divisi);
    public function getIncompleteTargetFromLastMonthByDivision($now, $divisi);
    public function updateActualLead($details, $now, $divisi);

    public function getAchievedLeadSalesByMonth($now);
    public function getAchievedHotLeadSalesByMonth($now);
    public function getAchievedInitConsultSalesByMonth($now);
    
}

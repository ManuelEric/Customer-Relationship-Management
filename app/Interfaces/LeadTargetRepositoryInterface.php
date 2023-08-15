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
    public function getAchievedContributionSalesByMonth($now);
    
    public function getAchievedLeadReferralByMonth($now);
    public function getAchievedHotLeadReferralByMonth($now);
    public function getAchievedInitConsultReferralByMonth($now);
    public function getAchievedContributionReferralByMonth($now);

    public function getAchievedLeadDigitalByMonth($now);
    public function getAchievedHotLeadDigitalByMonth($now);
    public function getAchievedInitConsultDigitalByMonth($now);
    public function getAchievedContributionDigitalByMonth($now);

    public function getAchievedRevenue($monthyear);
    public function getLeadDigital($monthYear, $prog_id = null);

    // public function getLeadSourceDigital($monthYear);
    // public function getConversionLeadDigital($monthYear);
}

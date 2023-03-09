<?php

namespace App\Interfaces;

interface CorporateRepositoryInterface
{
    public function getAllCorporateDataTables();
    public function getAllCorporate();
    public function getCountTotalCorporateByMonthly($monthYear);
    public function getCorporateById($corporateId);
    public function getCorporateByName($corp_name);
    public function deleteCorporate($corporateId);
    public function createCorporate(array $corporateDetails);
    public function updateCorporate($corporateId, array $newDetails);
    public function cleaningCorporate();
    public function getReportNewPartner($start_date, $end_date);
}

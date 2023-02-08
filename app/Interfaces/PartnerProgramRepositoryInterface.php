<?php

namespace App\Interfaces;

interface PartnerProgramRepositoryInterface
{


    public function getAllPartnerProgramsDataTables(array $filter);
    public function getAllPartnerProgramsByPartnerId($corpId);
    // public function getAllPartnerPrograms($partnerProgId);
    public function getAllPartnerProgramByStatusAndMonth($status, $monthYear);
    public function getStatusPartnerProgramByMonthly($monthYear);
    public function getReportPartnerPrograms($start_date, $end_date);
    public function getPartnerProgramById($partnerProgId);
    public function deletePartnerProgram($partnerProgId);
    public function createPartnerProgram(array $partnerPrograms);
    public function updatePartnerProgram($partnerProgId, array $partnerPrograms);
}

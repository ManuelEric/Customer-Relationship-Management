<?php

namespace App\Interfaces;

interface PartnerProgramRepositoryInterface
{

    
    public function getAllPartnerProgramsDataTables(array $filter);
    public function getAllPartnerProgramsByPartnerId($corpId);
    public function getPartnerProgramById($partnerProgId);
    public function deletePartnerProgram($partnerProgId);
    public function createPartnerProgram(array $partnerPrograms);
    public function updatePartnerProgram($partnerProgId, array $partnerPrograms);
}

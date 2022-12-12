<?php

namespace App\Interfaces;

interface PartnerProgramRepositoryInterface
{

    
    public function getAllPartnerProgramsDataTables();
    public function getAllPartnerProgramsByPartnerId($corpId);
    public function getPartnerProgramById($partnerProgId);
    public function deletePartnerProgram($partnerProgId);
    public function createPartnerProgram(array $partnerPrograms);
    public function updatePartnerProgram($partnerProgId, array $partnerPrograms);
}

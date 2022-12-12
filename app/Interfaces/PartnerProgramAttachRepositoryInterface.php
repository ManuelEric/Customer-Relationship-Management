<?php

namespace App\Interfaces;

interface PartnerProgramAttachRepositoryInterface
{

    public function getAllPartnerProgramAttachsByPartnerProgId($partnerProgramId);
    public function getPartnerProgramAttachById($corpProgAttachId);
    public function deletePartnerProgramAttach($corpProgAttachId);
    public function createPartnerProgramAttach(array $partnerProgramAttachs);
    public function updatePartnerProgramAttach($corpProgAttachId, array $partnerProgramAttachs);
}

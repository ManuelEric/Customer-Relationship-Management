<?php

namespace App\Interfaces;

interface PartnerAgreementRepositoryInterface
{
    public function getAllPartnerAgreementsByPartnerId($corpId);
    public function getPartnerAgreementById($partnerAgreementId);
    public function getPartnerAgreementByMonthly($monthYear, $type);
    public function deletePartnerAgreement($partnerAgreementId);
    public function createPartnerAgreement(array $partnerAgreements);
    public function updatePartnerAgreement($partnerAgreementId, array $partnerAgreements);
}

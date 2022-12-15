<?php

namespace App\Interfaces;

interface PartnerAggrementRepositoryInterface
{
    public function getAllPartnerAggrementsByPartnerId($corpId);
    public function getPartnerAggrementById($partnerAggrementId);
    public function deletePartnerAggrement($partnerAggrementId);
    public function createPartnerAggrement(array $partnerAggrements);
    public function updatePartnerAggrement($partnerAggrementId, array $partnerAggrements);
}

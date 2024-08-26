<?php

namespace App\Interfaces;

interface PartnerRepositoryInterface 
{
    public function getAllPartnerDataTables();
    public function getAllPartner();
    public function getPartnerById($partnerId);
    public function deletePartner($partnerId);
    public function createPartner(array $partnerDetails);
    public function updatePartner($partnerId, array $newDetails);
}
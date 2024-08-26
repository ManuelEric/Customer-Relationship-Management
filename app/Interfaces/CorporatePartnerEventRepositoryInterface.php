<?php

namespace App\Interfaces;

interface CorporatePartnerEventRepositoryInterface 
{
    public function getPartnerByEventId($eventId);
    public function addPartnerEvent($eventId, array $partnerDetails);
    public function destroyPartnerEvent($eventId, $corporateId);
}
<?php

namespace App\Interfaces;

interface LeadRepositoryInterface 
{
    public function getAllLeadDataTables();
    public function getAllLead();
    public function getLeadById($leadId);
    public function deleteLead($leadId);
    public function createLead(array $leadDetails);
    public function updateLead($leadId, array $newDetails);
}
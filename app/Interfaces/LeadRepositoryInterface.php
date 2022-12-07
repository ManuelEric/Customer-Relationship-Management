<?php

namespace App\Interfaces;

interface LeadRepositoryInterface 
{
    public function getAllLeadDataTables();
    public function getAllLead();
    public function getAllMainLead();
    public function getAllKOLlead();
    public function getLeadById($leadId);
    public function deleteLead($leadId);
    public function createLeads(array $leadDetails);
    public function createLead(array $leadDetails);
    public function updateLead($leadId, array $newDetails);

    # 
    public function getAllLeadFromV1();
}
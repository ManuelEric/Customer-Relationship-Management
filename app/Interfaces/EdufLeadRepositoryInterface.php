<?php

namespace App\Interfaces;

interface EdufLeadRepositoryInterface
{
    public function getAllEdufairLeadDataTables();
    public function getAllEdufairLead();
    public function getEdufairLeadById($edufLId);
    public function deleteEdufairLead($edufLId);
    public function createEdufairLead(array $edufairLeadDetails);
    public function createEdufairLeads(array $edufairLeadDetails);
    public function updateEdufairLead($edufLId, array $newDetails);
    public function getEdufairLeadByYear($year);
    public function getAllEdufFromCRM();
}

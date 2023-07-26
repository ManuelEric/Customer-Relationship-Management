<?php

namespace App\Interfaces;

interface ClientLeadTrackingRepositoryInterface 
{
    public function getAllClientLeadTracking();
    public function getAllClientLeadTrackingById($id);
    public function getAllClientLeadTrackingByClientId($client_id);
    public function updateClientLeadTrackingByType($clientId, $initProgId, $type, array $leadTrackingDetails);
    public function getHistoryClientLead($client_id);
    public function updateClientLeadTracking($clientId, $initProgId, array $leadTrackingDetails);
    public function createClientLeadTracking(array $leadTrackingDetails);

}
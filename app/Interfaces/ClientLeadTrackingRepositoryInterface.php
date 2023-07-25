<?php

namespace App\Interfaces;

interface ClientLeadTrackingRepositoryInterface 
{
    public function getAllClientLeadTracking();
    public function getAllClientLeadTrackingById($id);
    public function updateClientLeadTracking($clientId, $initProgId, array $leadTrackingDetails);
    public function createClientLeadTracking(array $leadTrackingDetails);

}
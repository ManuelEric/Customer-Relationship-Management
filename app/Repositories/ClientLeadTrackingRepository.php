<?php

namespace App\Repositories;

use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Models\Asset;
use App\Models\ClientLeadTracking;
use App\Models\InitialProgram;
use App\Models\v1\Asset as CRMAsset;
use DataTables;
use Illuminate\Support\Facades\DB;

class ClientLeadTrackingRepository implements ClientLeadTrackingRepositoryInterface 
{
    public function getAllClientLeadTracking()
    {
        return ClientLeadTracking::orderBy('id', 'asc')->get();
    }

    public function getAllClientLeadTrackingById($id) 
    {
        return ClientLeadTracking::where('id', $id)->first();
    }

    public function updateClientLeadTracking($clientId, $initProgId, array $leadTrackingDetails) 
    {
        return ClientLeadTracking::where('client_id', $clientId)->where('initialprogram_id', $initProgId)->update($leadTrackingDetails);
    }

    public function createClientLeadTracking(array $leadTrackingDetails) 
    {
        return ClientLeadTracking::create($leadTrackingDetails);
    }
}
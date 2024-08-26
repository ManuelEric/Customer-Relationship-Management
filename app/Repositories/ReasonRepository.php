<?php

namespace App\Repositories;

use App\Interfaces\ReasonRepositoryInterface;
use App\Models\Reason;
use App\Models\v1\Reason as V1Reason;
use DataTables;

class ReasonRepository implements ReasonRepositoryInterface
{

    public function getAllReasons()
    {
        return Reason::all();
    }

    public function getReasonByType($type)
    {
        return Reason::where('type', $type)->get();
    }

    public function getReasonById($reasonId)
    {
        return Reason::find($reasonId);
    }

    public function getReasonByReasonName($reasonName)
    {
        return Reason::where('reason_name', 'like', '%'.$reasonName.'%')->first();
    }
    
    public function getReasonByName($reasonName)
    {
        return Reason::where('reason_name', $reasonName)->first();
    }

    public function deleteReason($reasonId)
    {
        return Reason::destroy($reasonId);
    }

    public function createReason(array $reasons)
    {
        return Reason::create($reasons);
    }

    public function createReasons(array $reasonDetails)
    {
        return Reason::insert($reasonDetails);
    }

    public function updateReason($reasonId, array $newReasons)
    {
        return Reason::find($reasonId)->update($newReasons);
    }

    # CRM
    public function getAllReasonFromCRM()
    {
        return V1Reason::all();
    }
}

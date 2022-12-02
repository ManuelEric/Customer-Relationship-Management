<?php

namespace App\Repositories;

use App\Interfaces\ReasonRepositoryInterface;
use App\Models\Reason;
use DataTables;

class ReasonRepository implements ReasonRepositoryInterface
{

    public function getAllReasons()
    {
        return Reason::all();
    }

    public function getReasonById($reasonId)
    {
        return Reason::find($reasonId);
    }

    public function deleteReason($reasonId)
    {
        return Reason::destroy($reasonId);
    }

    public function createReason(array $reasons)
    {
        return Reason::create($reasons);
    }

    public function updateReason($reasonId, array $newReasons)
    {
        return Reason::find($reasonId)->update($newReasons);
    }
}

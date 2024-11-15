<?php

namespace App\Repositories;

use App\Interfaces\PositionRepositoryInterface;
use App\Models\Position;
use DataTables;

class PositionRepository implements PositionRepositoryInterface
{
    public function getAllPositionDataTables()
    {
        return Datatables::eloquent(Position::query())->make(true);
    }

    public function getAllPositions()
    {
        return Position::all();
    }

    public function getPositionById($id)
    {
        return Position::find($id);
    }

    public function getPositionByName($positionName)
    {
        return Position::where('position_name', '=', $positionName)->first();
    }

    public function deletePosition($positionId)
    {
        return Position::destroy($positionId);
    }

    public function createPositions(array $positionDetails)
    {
        return Position::insert($positionDetails);
    }

    public function createPosition(array $positionDetails)
    {
        return Position::create($positionDetails);
    }

    public function updatePosition($positionId, array $newDetails)
    {
        return tap(Position::whereId($positionId)->first())->update($newDetails);
    }
}
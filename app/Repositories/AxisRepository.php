<?php

namespace App\Repositories;

use App\Interfaces\AxisRepositoryInterface;
use App\Models\Axis;
use Illuminate\Support\Facades\DB;

class AxisRepository implements AxisRepositoryInterface
{
    public function getAxisByType($type)
    {
        return Axis::where('type', $type)->first();
    }

    public function createAxis(array $axis)
    {
        return Axis::create($axis);
    }

    public function updateAxis($id, array $axis)
    {
        return Axis::whereId($id)->update($axis);
    }
}

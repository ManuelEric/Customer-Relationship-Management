<?php

namespace App\Repositories;

use App\Interfaces\TargetSignalRepositoryInterface;
use App\Models\TargetSignal;

class TargetSignalRepository implements TargetSignalRepositoryInterface
{
    public function getTargetSignalByDivisi($divisi)
    {
        return TargetSignal::where('divisi', $divisi)->get();
    }

    public function getAllTargetSignal()
    {
        return TargetSignal::all();
    }
}

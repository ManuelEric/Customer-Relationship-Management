<?php

namespace App\Repositories;

use App\Interfaces\MajorRepositoryInterface;
use App\Models\Major;
use DataTables;

class MajorRepository implements MajorRepositoryInterface
{

    public function getAllMajorsDataTables()
    {
        return Datatables::eloquent(Major::query())->make(true);
    }

    public function getAllMajors()
    {
        return Major::all();
    }

    public function getAllActiveMajors()
    {
        return Major::where('active', true)->get();
    }

    public function getMajorById($id)
    {
        return Major::find($id);
    }

    public function getMajorByName($majorName)
    {
        return Major::whereRaw('lower(name) = ?', [strtolower($majorName)])->first();
    }

    public function deleteMajor($majorId)
    {
        return Major::destroy($majorId);
    }

    public function createMajors(array $majorDetails)
    {
        return Major::insert($majorDetails);
    }

    public function createMajor(array $majorDetails)
    {
        return Major::create($majorDetails);
    }

    public function updateMajor($majorId, array $newDetails)
    {
        return tap(Major::whereId($majorId)->first())->update($newDetails);
    }
}
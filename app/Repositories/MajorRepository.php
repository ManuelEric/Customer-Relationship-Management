<?php

namespace App\Repositories;

use App\Interfaces\MajorRepositoryInterface;
use App\Models\Major;

class MajorRepository implements MajorRepositoryInterface 
{

    public function getMajorByName($majorName)
    {
        return Major::whereRaw('LOWER(name) = (?)', [strtolower($majorName)])->first();
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
        return Major::whereId($majorId)->update($newDetails);
    }
}
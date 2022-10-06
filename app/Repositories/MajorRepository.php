<?php

namespace App\Repositories;

use App\Interfaces\MajorRepositoryInterface;
use App\Models\Major;

class MajorRepository implements MajorRepositoryInterface 
{
    public function createMajors(array $majorDetails) 
    {
        return Major::insert($majorDetails);
    }
}
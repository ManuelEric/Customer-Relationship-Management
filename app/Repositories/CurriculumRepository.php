<?php

namespace App\Repositories;

use App\Interfaces\CurriculumRepositoryInterface;
use App\Models\Curriculum;

class CurriculumRepository implements CurriculumRepositoryInterface 
{
    public function getAllCurriculum()
    {
        return Curriculum::orderBy('name', 'asc')->get();
    }
}
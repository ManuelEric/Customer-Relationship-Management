<?php

namespace App\Repositories;

use App\Interfaces\CurriculumRepositoryInterface;
use App\Models\Curriculum;
use DataTables;

class CurriculumRepository implements CurriculumRepositoryInterface 
{

    public function getAllCurriculumsDataTables()
    {
        return Datatables::eloquent(Curriculum::query())->make(true);
    }

    public function getAllCurriculums()
    {
        return Curriculum::orderBy('name', 'asc')->get();
    }

    public function getCurriculumById($curriculumId)
    {
        return Curriculum::find($curriculumId);
    }

    public function getCurriculumByName($curriculumName)
    {
        return Curriculum::where('name', $curriculumName)->first();
    }

    public function createOneCurriculum(array $curriculumDetails)
    {
        return Curriculum::create($curriculumDetails);
    }

    public function createCurriculum(array $curriculumDetails)
    {
        return Curriculum::insert($curriculumDetails);
    }

    public function deleteCurriculum($curriculumId)
    {
        return Curriculum::destroy($curriculumId);
    }

    public function updateCurriculum($curriculumId, array $newCurriculums)
    {
        return tap(Curriculum::find($curriculumId))->update($newCurriculums);
    }
}
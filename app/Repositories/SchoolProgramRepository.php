<?php

namespace App\Repositories;

use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Models\SchoolProgram;
use DataTables;

class SchoolProgramRepository implements SchoolProgramRepositoryInterface
{


    public function getAllSchoolProgramsById($schoolId)
    {
        return SchoolProgram::where('sch_id', $schoolId)->orderBy('id', 'asc')->get();
    }

    public function getSchoolProgramById($schoolProgramId)
    {
        return SchoolProgram::find($schoolProgramId);
    }

    public function deleteSchoolProgram($schoolProgramId)
    {
        return SchoolProgram::destroy($schoolProgramId);
    }

    public function createSchoolProgram(array $schoolPrograms)
    {
        return SchoolProgram::create($schoolPrograms);
    }

    public function updateSchoolProgram($schoolProgramId, array $newPrograms)
    {
        return SchoolProgram::find($schoolProgramId)->update($newPrograms);
    }
}

<?php

namespace App\Repositories;

use App\Interfaces\SchoolProgramAttachRepositoryInterface;
use App\Models\SchoolProgramAttach;
use DataTables;

class SchoolProgramAttachRepository implements SchoolProgramAttachRepositoryInterface
{


    public function getAllSchoolProgramAttachsBySchprogId($schoolProgramId)
    {
        return SchoolProgramAttach::where('schprog_id', $schoolProgramId)->orderBy('id', 'asc')->get();
    }

    public function getSchoolProgramAttachById($schProgAttachId)
    {
        return SchoolProgramAttach::find($schProgAttachId);
    }

    public function deleteSchoolProgramAttach($schProgAttachId)
    {
        return SchoolProgramAttach::destroy($schProgAttachId);
    }

    public function createSchoolProgramAttach(array $schoolProgramAttachs)
    {
        return SchoolProgramAttach::create($schoolProgramAttachs);
    }

    public function updateSchoolProgramAttach($schProgAttachId, array $newSchProgAttachs)
    {
        return SchoolProgramAttach::find($schProgAttachId)->update($newSchProgAttachs);
    }
}

<?php

namespace App\Repositories;

use App\Interfaces\InitialProgramRepositoryInterface;
use App\Models\Asset;
use App\Models\InitialProgram;
use App\Models\v1\Asset as CRMAsset;
use DataTables;
use Illuminate\Support\Facades\DB;

class InitialProgramRepository implements InitialProgramRepositoryInterface 
{
    public function getAllInitProg()
    {
        return InitialProgram::orderBy('id', 'asc')->get();
    }

    public function getInitProgById($id) 
    {
        return InitialProgram::where('id', $id)->first();
    }

    public function getInitProgByName($name) 
    {
        return InitialProgram::where('name', $name)->first();
    }
}
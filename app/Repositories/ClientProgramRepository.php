<?php

namespace App\Repositories;

use App\Interfaces\ClientProgramRepositoryInterface;
use App\Models\ClientProgram;
use DataTables;

class ClientProgramRepository implements ClientProgramRepositoryInterface 
{
    public function getAllClientProgramDataTables()
    {
        return Datatables::eloquent(ClientProgram::query())->make(true);
    }

    public function createClientProgram($clientProgramDetails)
    {
        return ClientProgram::create($clientProgramDetails);
    }

    
}
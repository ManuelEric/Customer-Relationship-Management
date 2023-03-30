<?php

namespace App\Repositories;

use App\Interfaces\MainProgRepositoryInterface;
use App\Models\MainProg;

class MainProgRepository implements MainProgRepositoryInterface 
{
    public function getAllMainProg()
    {
        return MainProg::all();
    }

    public function getMainProgById($mainProgId)
    {
        return MainProg::find($mainProgId);
    }

    public function getMainProgByName($progName)
    {
        return MainProg::where('prog_name', $progName)->first();
    }

    public function createMainProg($mainProgDetails)
    {
        return MainProg::create($mainProgDetails);
    }
}
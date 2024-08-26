<?php

namespace App\Repositories;

use App\Interfaces\SubProgRepositoryInterface;
use App\Models\MainProg;
use App\Models\SubProg;

class SubProgRepository implements SubProgRepositoryInterface 
{

    public function getSubProgByMainProgId($mainProg)
    {
        return SubProg::whereHas('mainProgram', function($query) use ($mainProg) {
            $query->where('id', $mainProg);
        })->get();
    }

    // public function getSubProgBySubProgId($subProg)
    // {
    //     return SubProg::whereHas('program', function($query) use ($subProg) {
    //         $query->where('id', $subProg);
    //     })->get();
    // }

    public function getSubProgById($subProgId)
    {
        return SubProg::find($subProgId);
    }

    public function getSubProgByMainProgName($mainProg)
    {
        return SubProg::whereHas('mainProgram', function($query) use ($mainProg) {
            $query->where('prog_name', $mainProg);
        })->get();
    }

    public function getSubProgBySubProgName($subProgName)
    {
        return SubProg::where('sub_prog_name', $subProgName)->first();
    }

    public function createSubProg($subProgDetails)
    {
        return SubProg::create($subProgDetails);
    }
}
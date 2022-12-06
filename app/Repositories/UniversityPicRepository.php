<?php

namespace App\Repositories;

use App\Interfaces\UniversityPicRepositoryInterface;
use App\Models\UniversityPic;

class UniversityPicRepository implements UniversityPicRepositoryInterface 
{

    public function getAllUniversityPicByUniversityId($universityId)
    {
        return UniversityPic::where('univ_id', $universityId)->get();
    }

    public function getAllUniversityPic()
    {
        return UniversityPic::orderBy('name', 'asc')->get();
    }

    public function getUniversityPicById($picId)
    {
        return UniversityPic::find($picId);
    }

    public function deleteUniversityPic($picId)
    {
        return UniversityPic::destroy($picId);
    }

    public function createUniversityPic(array $picDetails)
    {
        return UniversityPic::create($picDetails);
    }

    public function updateUniversityPic($picId, array $newDetails)
    {
        return UniversityPic::whereId($picId, $newDetails)->update($newDetails);
    }
}
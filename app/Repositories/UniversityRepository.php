<?php

namespace App\Repositories;

use App\Interfaces\UniversityRepositoryInterface;
use App\Models\University;

class UniversityRepository implements UniversityRepositoryInterface 
{
    public function getAllUniversities()
    {
        return University::all();   
    }

    public function getUniversityById($universityId)
    {
        return University::findOrFail($universityId);
    }

    public function deleteUniversity($universityId) 
    {
        return University::destroy($universityId);
    }

    public function createUniversity(array $universityDetails) 
    {
        return University::create($universityDetails);
    }

    public function updateUniversity($universityId, array $newDetails) 
    {
        return University::whereUniversityId($universityId)->update($newDetails);
    }
}
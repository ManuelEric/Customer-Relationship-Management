<?php

namespace App\Repositories;

use App\Interfaces\UniversityRepositoryInterface;
use App\Models\CountryTranslations;
use App\Models\University;
use Illuminate\Http\JsonResponse;
use DataTables;

class UniversityRepository implements UniversityRepositoryInterface
{
    public function getAllUniversitiesDataTables()
    {
        return Datatables::eloquent(University::query())->make(true);
    }

    public function getAllUniversities()
    {
        return University::all();
    }

    public function getUniversityById($universityId)
    {
        return University::findOrFail($universityId);
    }

    public function getUniversityByUnivId($universityId)
    {
        return University::whereUniversityId($universityId);
    }

    public function getUniversityByName($universityName)
    {
        return University::whereRaw('LOWER(univ_name) = (?)', [strtolower($universityName)])->first();
    }

    public function deleteUniversity($universityId)
    {
        return University::destroy($universityId);
    }

    public function createUniversities(array $universityDetails)
    {
        return University::insert($universityDetails);
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
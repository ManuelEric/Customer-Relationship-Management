<?php

namespace App\Repositories;

use App\Interfaces\UniversityRepositoryInterface;
use App\Models\CountryTranslations;
use App\Models\University;
use App\Models\v1\University as V1University;
use Illuminate\Http\JsonResponse;
use DataTables;
use Illuminate\Database\Eloquent\Collection;

class UniversityRepository implements UniversityRepositoryInterface
{
    public function getAllUniversitiesDataTables()
    {
        return Datatables::eloquent(University::leftJoin('tbl_tag', function($join) {
            $join->on('tbl_univ.tag', '=', 'tbl_tag.id');
        })->select('tbl_univ.*', 'tbl_tag.name as tag_name'))
            ->make(true);
    }

    public function getAllUniversities()
    {
        return University::whereNotNull('univ_name')->orderBy('univ_name', 'asc')->get();
    }

    public function getAllUniversitiesByCountries(array $countries)
    {
        return University::whereIn('univ_country', $countries)->get();
    }

    public function getAllUniversitiesByTag(array $tags)
    {
        $universities = University::whereIn('tag', $tags)->get();
        
        if (in_array('7', $tags)) {
            $tags = ['1', '2', '3', '4', '5', '6'];
            $other_universities = University::whereNotIn('tag', $tags)->orWhereNull('tag')->get();

            if ($other_universities)
                $universities = $universities->merge($other_universities);
        }

        return $universities;
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

    public function getCountryNameFromUniversity()
    {
        return University::whereNotNull('univ_country')->select('univ_country')->groupBy('univ_country')->get();
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

    # CRM
    public function getAllUniversitiesFromCRM()
    {
        return V1University::all();
    }
}
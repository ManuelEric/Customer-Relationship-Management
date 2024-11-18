<?php

namespace App\Repositories;

use App\Interfaces\UniversityRepositoryInterface;
use App\Models\CountryTranslations;
use App\Models\UnivCountry;
use App\Models\University;
use App\Models\v1\University as V1University;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use DataTables;
use Illuminate\Database\Eloquent\Collection;

class UniversityRepository implements UniversityRepositoryInterface
{
    public function getAllUniversitiesDataTables()
    {
        return Datatables::eloquent(University::
        leftJoin('tbl_country', function ($join) {
            $join->on('tbl_univ.univ_country', '=', 'tbl_country.id');
        })->
        leftJoin('tbl_tag', function ($join) {
            $join->on('tbl_country.tag', '=', 'tbl_tag.id');
        })
        ->select('tbl_univ.*', 'tbl_country.name as country_name', 'tbl_tag.name as tag_name'))
            ->make(true);
    }

    public function getAllUniversities()
    {
        return University::whereNotNull('univ_name')->orderBy('univ_name', 'asc')->get();
    }

    public function getUniversityByMonthly($monthYear, $type)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        $query = University::query();

        if ($type == 'all') {
            $query->whereYear('created_at', '<=', $year)
                ->whereMonth('created_at', '<=', $month);
        } else {
            $query->whereYear('created_at', '=', $year)
                ->whereMonth('created_at', '=', $month);
        }

        switch ($type) {
            case 'all':
                return $query->count();
                break;
            case 'monthly':
                return $query->count();
                break;
            case 'list':
                return $query->get();
                break;
        }
    }

    public function getAllUniversitiesByCountries(array $countries)
    {
        return University::whereIn('univ_country', $countries)->get();
    }

    public function getAllUniversitiesByTag(array $tags)
    {
        $universities = University::with('tags')->whereIn('univ_country', $tags)->get();

        if (in_array('7', $tags)) { # 7 means Tag : Other
            $tags = ['1', '2', '3', '4', '5', '6'];
            $other_universities = University::with('tags')->whereNotIn('univ_country', $tags)->orWhereNull('univ_country')->get();

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
        return UnivCountry::has('universities')->select('id', 'name')->orderBy('name', 'ASC')->get();
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
        return tap(University::whereUniversityId($universityId))->update($newDetails);
    }

    public function getReportNewUniversity($start_date = null, $end_date = null)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        if (isset($start_date) && isset($end_date)) {
            return University::whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)
                ->get();
        } else if (isset($start_date) && !isset($end_date)) {
            return University::whereDate('created_at', '>=', $start_date)
                ->get();
        } else if (!isset($start_date) && isset($end_date)) {
            return University::whereDate('created_at', '<=', $end_date)
                ->get();
        } else {
            return University::whereBetween('created_at', [$firstDay, $lastDay])
                ->get();
        }
    }

    # CRM
    public function getAllUniversitiesFromCRM()
    {
        return V1University::all();
    }

    public function getUniversityFromCRMByUnivId($univId)
    {
        return V1University::whereRaw('univ_id = ?', [trim($univId)])->first();
    }
}

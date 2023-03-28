<?php

namespace App\Repositories;

use App\Interfaces\CountryRepositoryInterface;
use App\Models\CountryTranslations;
use App\Models\RegionTranslations;
use Illuminate\Http\JsonResponse;

class CountryRepository implements CountryRepositoryInterface 
{
    public function getAllCountries()
    {
        return CountryTranslations::where('locale', 'en')->orderBy('name', 'asc')->get();
    }

    public function getCountryNameByUnivCountry($univCountry)
    {
        return CountryTranslations::where('name', 'like', '%'.$univCountry.'%')->first();
    }

    public function getRegionByRegionId($regionId)
    {
        return RegionTranslations::where('lc_region_id', $regionId)->where('locale', 'en')->first();
    }

    public function getAllRegionByLocale($locale)
    {
        return RegionTranslations::where('locale', $locale)->get();
    }
}
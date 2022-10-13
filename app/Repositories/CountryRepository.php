<?php

namespace App\Repositories;

use App\Interfaces\CountryRepositoryInterface;
use App\Models\CountryTranslations;
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
}
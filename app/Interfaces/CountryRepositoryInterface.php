<?php

namespace App\Interfaces;

interface CountryRepositoryInterface 
{
    public function getAllCountries();
    public function getCountryNameByUnivCountry($univCountry);
    public function getRegionByRegionId($regionId);
}
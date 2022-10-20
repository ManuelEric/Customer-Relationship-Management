<?php

namespace App\Interfaces;

interface SchoolRepositoryInterface 
{
    public function getAllSchools();
    public function cleaningSchool();
    public function cleaningSchoolDetail();
}
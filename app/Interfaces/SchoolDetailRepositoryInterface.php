<?php

namespace App\Interfaces;

interface SchoolDetailRepositoryInterface 
{
    public function getAllSchoolDetailDataTables($schoolId);
    public function getAllSchoolDetails($schoolId);
    public function getSchoolDetailById($schoolDetailId);
    public function deleteSchoolDetail($schoolDetailId);
    public function createSchoolDetail(array $schoolDetails);
    public function updateSchoolDetail($schoolDetailId, array $schoolDetails);
}
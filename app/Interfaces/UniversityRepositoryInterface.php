<?php

namespace App\Interfaces;

interface UniversityRepositoryInterface 
{
    public function getAllUniversitiesDataTables();
    public function getAllUniversities();
    public function getUniversityById($universityId);
    public function getUniversityByUnivId($universityId);
    public function getUniversityByName($universityName);
    public function deleteUniversity($universityId);
    public function createuniversities(array $universityDetails);
    public function createUniversity(array $universityDetails);
    public function updateUniversity($universityId, array $newDetails);
}
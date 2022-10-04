<?php

namespace App\Interfaces;

interface UniversityRepositoryInterface 
{
    public function getAllUniversities();
    public function getUniversityById($universityId);
    public function deleteUniversity($universityId);
    public function createUniversity(array $universityDetails);
    public function updateUniversity($universityId, array $newDetails);
}
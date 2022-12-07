<?php

namespace App\Interfaces;

interface MajorRepositoryInterface 
{
    public function getAllMajorsDataTables();
    public function getAllMajors();
    public function getMajorByName($majorName);
    public function deleteMajor($majorId);
    public function createMajors(array $majorDetails);
    public function createMajor(array $majorDetails);
    public function updateMajor($majorId, array $newDetails);
}
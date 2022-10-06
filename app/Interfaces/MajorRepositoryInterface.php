<?php

namespace App\Interfaces;

interface MajorRepositoryInterface 
{
    public function getMajorByName($majorName);
    public function createMajors(array $majorDetails);
}
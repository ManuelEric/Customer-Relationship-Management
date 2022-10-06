<?php

namespace App\Interfaces;

interface EmployeeRepositoryInterface 
{
    public function getDistinctDepartment();
    public function getDistinctUniversity();
    public function getDistinctMajor();
    public function getDistinctMajorMagister();
    public function getAllEmployees();
}
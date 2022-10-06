<?php

namespace App\Interfaces;

interface EmployeeRepositoryInterface 
{
    public function getDistinctDepartment();
    public function getDistinctUniversity();
}
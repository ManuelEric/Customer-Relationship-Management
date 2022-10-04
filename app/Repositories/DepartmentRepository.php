<?php

namespace App\Repositories;

use App\Interfaces\DepartmentRepositoryInterface;
use App\Models\Department;

class DepartmentRepository implements DepartmentRepositoryInterface 
{
    public function getAllDepartments()
    {
        return Department::all();
    }

    public function createDepartments(array $departmentDetails) 
    {
        return Department::insert($departmentDetails);
    }
}
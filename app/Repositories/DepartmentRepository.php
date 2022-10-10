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

    public function getDepartmentByName($departmentName)
    {
        return Department::where('dept_name', '=', $departmentName)->first();
    }

    public function createDepartments(array $departmentDetails) 
    {
        return Department::insert($departmentDetails);
    }

    public function createDepartment(array $departmentDetails)
    {
        return Department::create($departmentDetails);
    }
}
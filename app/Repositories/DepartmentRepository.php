<?php

namespace App\Repositories;

use App\Interfaces\DepartmentRepositoryInterface;
use App\Models\Department;
use App\Models\User;
use DataTables;

class DepartmentRepository implements DepartmentRepositoryInterface 
{
    public function getAllDepartment()
    {
        return Department::orderBy('dept_name', 'asc')->get();
    }

    public function getEmployeeByDepartment($departmentId)
    {
        $department = Department::find($departmentId);
        return $department->users;
    }
}
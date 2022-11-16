<?php

namespace App\Repositories;

use App\Interfaces\DepartmentRepositoryInterface;
use App\Models\Department;
use DataTables;

class DepartmentRepository implements DepartmentRepositoryInterface 
{
    public function getAllDepartment()
    {
        return Department::orderBy('dept_name', 'asc')->get();
    }
}
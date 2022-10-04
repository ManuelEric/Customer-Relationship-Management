<?php

namespace App\Repositories;

use App\Interfaces\EmployeeRepositoryInterface;
use App\Models\v1\Employee;

class EmployeeRepository implements EmployeeRepositoryInterface 
{
    public function getDistinctDepartment()
    {
        return Employee::orderBy('empl_department', 'asc')->select('empl_department as dept_name')->distinct()->get();
    }

    public function getAllEmployees()
    {
        return Employee::all();
    }
}
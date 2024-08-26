<?php

namespace App\Repositories;

use App\Interfaces\EmployeeRepositoryInterface;
use App\Models\v1\Employee;

class EmployeeRepository implements EmployeeRepositoryInterface 
{
    public function getDistinctDepartment()
    {
        return Employee::where('empl_department', '!=', '')->where('empl_department', '!=', null)->orderBy('empl_department', 'asc')->select('empl_department as dept_name')->distinct()->get();
    }

    public function getDistinctUniversity()
    {
        return Employee::where('empl_graduatefr', '!=', '')->where('empl_graduatefr', '!=', null)->orderBy('empl_graduatefr', 'asc')->select('empl_graduatefr')->distinct()->get();
    }

    public function getDistinctMajor()
    {
        return Employee::where('empl_major', '!=', '')->where('empl_major', '!=', null)->orderBy('empl_major', 'asc')->select('empl_major')->distinct()->get();
    }

    public function getDistinctMajorMagister()
    {
        return Employee::where('empl_major_magister', '!=', '')->where('empl_major_magister', '!=', null)->orderBy('empl_major_magister', 'asc')->select('empl_major_magister')->distinct()->get();
    }

    public function getAllEmployees()
    {
        return Employee::orderBy('empl_id', 'asc')->get();
    }
}
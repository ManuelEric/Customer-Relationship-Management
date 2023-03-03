<?php

namespace App\Interfaces;

interface DepartmentRepositoryInterface 
{
    public function getAllDepartment();
    public function getEmployeeByDepartment(int $departmentId);
}
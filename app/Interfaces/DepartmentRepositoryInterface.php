<?php

namespace App\Interfaces;

interface DepartmentRepositoryInterface 
{
    public function getAllDepartments();
    public function getDepartmentByName($departmentName);
    public function createDepartments(array $departmentDetails);
    public function createDepartment(array $departmentDetails);
}
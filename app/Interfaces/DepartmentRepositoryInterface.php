<?php

namespace App\Interfaces;

interface DepartmentRepositoryInterface 
{
    public function getAllDepartmentDataTables();
    public function getAllDepartments();
    public function getDepartmentByName($departmentName);
    public function deleteDepartment($departmentId);
    public function createDepartments(array $departmentDetails);
    public function createDepartment(array $departmentDetails);
    public function updateDepartment($departmentId, array $newDetails);
}
<?php

namespace App\Interfaces;

interface MenuRepositoryInterface 
{
    public function getMenu();
    public function getUserAccess(int $userId);
    public function getUserAccessById($userId, $menuId);
    public function getDepartmentAccess(int $departmentId);
    public function getDepartmentAccessByMenuId($departmentId, $menuId);
    public function createAccessToUser(int $userId, array $newDetails);
    public function createAccessToDepartment(int $departmentId, array $newDetails);
    public function deleteUserAccess($userId, $deletedDetails);
    public function deleteDepartmentAccess($departmentId, $deletedDetails);
}
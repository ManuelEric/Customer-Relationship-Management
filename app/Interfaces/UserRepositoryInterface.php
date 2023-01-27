<?php

namespace App\Interfaces;

interface UserRepositoryInterface 
{
    public function getAllUsersByRoleDataTables($role);
    public function getAllUsers();
    public function getAllUsersByRole($role);
    public function getUserById($userId);
    public function getUserByExtendedId($extendedId);
    public function getUserByFullNameOrEmail($userName, $userEmail);
    public function createUsers(array $userDetails);
    public function createUser(array $userDetails);
    public function updateUser($userId, array $newDetails);
    public function updateStatusUser($userId, $newStatus);
    public function deleteUserType($userTypeId);
    public function getUserRoles($userId, $roleName);
    public function cleaningUser();
}
<?php

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function getAllUsersByRoleDataTables($role);
    public function getAllUsers();
    public function getAllUsersWithoutUUID();
    public function getAllUsersByRole($role);
    public function getAllUsersByDepartmentAndRole($role, $department);
    public function getAllUsersProbationContracts();
    public function getUserById($userId);
    public function getUserByfirstName($first_name);
    public function getUserByExtendedId($extendedId);
    public function getUserByFullNameOrEmail($userName, $userEmail);
    public function createUsers(array $userDetails);
    public function createUser(array $userDetails);
    public function updateUser($userId, array $newDetails);
    public function updateStatusUser($userId, $newStatus);
    public function deleteUserType($userTypeId);
    public function getUserRoles($userId, $roleName);
    public function cleaningUser();
    public function createUserEducation(User $user, array $userEducationDetails);
    public function createUserRole(User $user, array $userRoleDetails);
    public function createUserType(User $user, array $userTypeDetails);
}

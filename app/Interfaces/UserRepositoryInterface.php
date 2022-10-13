<?php

namespace App\Interfaces;

interface UserRepositoryInterface 
{
    public function getAllUsers();
    public function getUserById($userId);
    public function getUserByExtendedId($extendedId);
    public function getUserByFullNameOrEmail($userName, $userEmail);
    public function createUsers(array $userDetails);
    public function createUser(array $userDetails);
    public function getUserRoles($userId, $roleName);
    public function cleaningUser();
}
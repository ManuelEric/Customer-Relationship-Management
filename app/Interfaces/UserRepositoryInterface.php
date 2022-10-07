<?php

namespace App\Interfaces;

interface UserRepositoryInterface 
{
    public function getAllUsers();
    public function getUserById($userId);
    public function getUserByExtendedId($extendedId);
    public function createUsers(array $userDetails);
    public function createUser(array $userDetails);
}
<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface 
{
    public function getAllUsers()
    {
        return User::all();
    }

    public function getUserById($userId)
    {
        return User::findOrFail($userId);
    }

    public function getUserByExtendedId($extendedId)
    {
        return User::whereExtendedId($extendedId);
    }

    public function createUsers(array $userDetails)
    {
        return User::insert($userDetails);
    }

    public function createUser(array $userDetails)
    {
        return User::create($userDetails);
    }

}
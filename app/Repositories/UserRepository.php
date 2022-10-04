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

    public function createUser(array $userDetails)
    {
        return User::create();
    }

}
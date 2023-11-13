<?php

namespace App\Repositories;

use App\Interfaces\UserTypeRepositoryInterface;
use App\Models\User;
use App\Models\UserType;

class UserTypeRepository implements UserTypeRepositoryInterface 
{

    public function getAllUserType()
    {
        return UserType::all();
    }

    public function getActiveUserTypeByUserId($userId)
    {
        $user = User::find($userId);
        return $user->user_type()->orderBy('id', 'asc')->first();
        
    }

    public function getUserTypeByTypeName(string $typeName)
    {
        $typeName = str_replace(' ', '-', trim($typeName));
        return UserType::where('type_name', $typeName)->first();
    }

}
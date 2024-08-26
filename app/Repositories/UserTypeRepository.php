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
        $user = User::isActive()->
                where('id', $userId)->
                select([
                    'id', 'first_name', 'last_name', 'email', 'phone'
                ])->
                first();
        return $user->user_type()->wherePivot('status', 1)->latest('created_at')->first();
        
    }

    public function getUserTypeByTypeName(string $typeName)
    {
        $typeName = str_replace(' ', '-', trim($typeName));
        return UserType::where('type_name', $typeName)->first();
    }

}
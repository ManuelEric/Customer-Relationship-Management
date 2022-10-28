<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserRepository implements UserRepositoryInterface 
{
    public function getAllUsers()
    {
        return User::all();
    }

    public function getAllUsersByRole($role)
    {
        return User::whereHas('roles', function ($query) use ($role) {
            $query->where('role_name', $role);
        })->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
    }

    public function getUserById($userId)
    {
        return User::findOrFail($userId);
    }

    public function getUserByExtendedId($extendedId)
    {
        return User::whereExtendedId($extendedId);
    }

    public function getUserByFullNameOrEmail($userName, $userEmail)
    {
        $userName = explode(' ', $userName);

        return User::where(function ($extquery) use ($userName) {

            # search word by word 
            # and loop based on name length
            for ($i = 0 ; $i < count($userName) ; $i++) {

                # looping at least two times
                if ($i <= 1)
                    $extquery = $extquery->whereRaw("CONCAT(first_name, ' ', last_name) like ?", ['%'.$userName[$i].'%']);

            }

        })->orWhere('email', $userEmail)->first();
    }

    public function createUsers(array $userDetails)
    {
        return User::insert($userDetails);
    }

    public function createUser(array $userDetails)
    {
        return User::create($userDetails);
    }

    public function updateExtendedId($newDetails)
    {
        
    }

    public function getUserRoles($userId, $roleName)
    {
        return User::where('id', $userId)->whereHas('roles', function (Builder $query) use ($roleName) {
            $query->where('role_name', '=', $roleName);
        })->first();
    }

    public function cleaningUser()
    {
        User::where('last_name', '=', '')->update(
            [
                'last_name' => null
            ]
        );

        User::where('address', '=', '')->update(
            [
                'address' => null
            ]
        );

        User::where('emergency_contact', '=', '')->orWhere('emergency_contact', '=', '-')->update(
            [
                'emergency_contact' => null
            ]
        );

        User::where('password', '=', '')->update(
            [
                'password' => null
            ]
        );
    }

}
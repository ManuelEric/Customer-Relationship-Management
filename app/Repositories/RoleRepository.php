<?php

namespace App\Repositories;

use App\Interfaces\RoleRepositoryInterface;
use App\Models\Role;

class RoleRepository implements RoleRepositoryInterface 
{
    public function getRoleByName($roleName)
    {
        return Role::whereRaw('LOWER(role_name) = (?)', [strtolower($roleName)])->first();
    }
}
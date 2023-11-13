<?php

namespace App\Interfaces;

interface UserTypeRepositoryInterface 
{
    public function getAllUserType();
    public function getActiveUserTypeByUserId($userId);
    public function getUserTypeByTypeName(string $typeName);
}
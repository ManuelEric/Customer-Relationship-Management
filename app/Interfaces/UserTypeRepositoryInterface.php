<?php

namespace App\Interfaces;

interface UserTypeRepositoryInterface 
{
    public function getAllUserType();
    public function getUserTypeByTypeName(string $typeName);
}
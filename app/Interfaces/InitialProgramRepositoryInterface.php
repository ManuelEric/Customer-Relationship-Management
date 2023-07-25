<?php

namespace App\Interfaces;

interface InitialProgramRepositoryInterface 
{
    public function getAllInitProg();
    public function getInitProgById($id);
    public function getInitProgByName($name);

}
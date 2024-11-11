<?php

namespace App\Interfaces;

interface MainProgRepositoryInterface 
{
    public function rnGetAllMainProg();
    public function getMainProgById($mainProgId);
    public function getMainProgByName($progName);
    public function createMainProg($mainProgDetails);
}
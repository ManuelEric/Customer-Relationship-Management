<?php

namespace App\Interfaces;

interface SubProgRepositoryInterface 
{
    public function getSubProgByMainProgId($mainProg);
    public function getSubProgById($subProgId);
    public function getSubProgByMainProgName($mainProg);
    public function getSubProgBySubProgName($subProgName);
    public function createSubProg($subProgDetails);
}
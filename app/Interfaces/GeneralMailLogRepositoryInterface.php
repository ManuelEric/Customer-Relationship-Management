<?php

namespace App\Interfaces;

interface GeneralMailLogRepositoryInterface 
{
    public function getStatus($identifier);
    public function createLog(array $details);
    public function removeLog($identifier);
}
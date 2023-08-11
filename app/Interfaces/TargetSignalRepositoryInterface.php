<?php

namespace App\Interfaces;

interface TargetSignalRepositoryInterface 
{
    public function getTargetSignalByDivisi($divisi);
    public function getAllTargetSignal();

}
<?php

namespace App\Interfaces;

interface ProgramRepositoryInterface 
{
    public function getAllProgramsDataTables();
    public function getAllPrograms();
    public function getAllProgramByType($type);
    public function getProgramById($programId);
    public function deleteProgram($programId);
    public function createProgram(array $programDetails);
    public function updateProgram($programId, array $newDetails);
    public function cleaningProgram();
}
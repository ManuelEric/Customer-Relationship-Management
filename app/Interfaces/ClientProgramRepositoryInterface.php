<?php

namespace App\Interfaces;

interface ClientProgramRepositoryInterface
{
    public function getAllClientProgramDataTables();
    public function createClientProgram(array $clientProgramDetails);
}
<?php

namespace App\Interfaces;

interface ClientProgramLogMailRepositoryInterface
{
    public function getClientProgramLogMail();
    public function createClientProgramLogMail(array $logMailDetails);
    public function updateClientProgramLogMail($id, $newDetails);
}

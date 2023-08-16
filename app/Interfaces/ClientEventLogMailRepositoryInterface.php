<?php

namespace App\Interfaces;

interface ClientEventLogMailRepositoryInterface
{
    public function createClientEventLogMail(array $logMailDetails);
    public function updateClientEventLogMail($id, $newDetails);
}

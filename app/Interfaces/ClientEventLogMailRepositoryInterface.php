<?php

namespace App\Interfaces;

interface ClientEventLogMailRepositoryInterface
{
    public function getClientEventLogMail();
    public function createClientEventLogMail(array $logMailDetails);
    public function updateClientEventLogMail($id, $newDetails);
}

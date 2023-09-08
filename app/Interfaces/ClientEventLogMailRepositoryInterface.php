<?php

namespace App\Interfaces;

interface ClientEventLogMailRepositoryInterface
{
    public function getClientEventLogMail($category);
    public function createClientEventLogMail(array $logMailDetails);
    public function updateClientEventLogMail($id, $newDetails);
}

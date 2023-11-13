<?php

namespace App\Interfaces;

interface ClientEventLogMailRepositoryInterface
{
    public function getClientEventLogMail();
    public function getClientEventLogMailByClientEventIdAndCategory($clientEventId, $category);
    public function createClientEventLogMail(array $logMailDetails);
    public function updateClientEventLogMail($id, $newDetails);
}

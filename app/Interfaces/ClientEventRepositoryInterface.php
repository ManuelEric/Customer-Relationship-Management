<?php

namespace App\Interfaces;

interface ClientEventRepositoryInterface
{
    public function getAllClientEventDataTables();
    public function getReportClientEvents($eventId);
    public function getReportClientEventsGroupByRoles($eventId);
    public function getConversionLead($eventId);
    public function getAllClientEventByClientId($clientId);
    public function getClientEventById($clientEventId);
    public function deleteClientEvent($clientEventId);
    public function createClientEvent(array $clientEvents);
    public function updateClientEvent($clientEventId, array $clientEvents);
}

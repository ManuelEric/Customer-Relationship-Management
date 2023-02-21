<?php

namespace App\Interfaces;

interface ClientEventRepositoryInterface
{
    public function getAllClientEventDataTables();
    public function getAllClientEvents($eventId);
    public function getAllClientEventsGroupByRoles($eventId);
    public function getConversionLead($filter);
    public function getReportClientEvents($eventId);
    public function getReportClientEventsGroupByRoles($eventId);
    public function getAllClientEventByClientId($clientId);
    public function getClientEventById($clientEventId);
    public function deleteClientEvent($clientEventId);
    public function createClientEvent(array $clientEvents);
    public function updateClientEvent($clientEventId, array $clientEvents);
}

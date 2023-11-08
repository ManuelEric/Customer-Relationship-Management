<?php

namespace App\Interfaces;

interface ClientEventRepositoryInterface
{
    public function getAllClientEventDataTables(array $filter);
    public function getAllClientEventByClientIdDataTables($clientId);
    public function getAllClientEventByClientId($clientId);
    public function getReportClientEvents($eventId);
    public function getReportClientEventsDataTables($eventId);
    public function getReportClientEventsGroupByRoles($eventId);
    public function getConversionLead($filter);
    public function getClientEventByClientId($clientId);
    public function getClientEventByClientIdAndEventId($clientId, $eventId);
    public function getClientEventById($clientEventId);
    public function getClientEventByEventId($eventId);
    public function deleteClientEvent($clientEventId);
    public function createClientEvent(array $clientEvents);
    public function updateClientEvent($clientEventId, array $clientEvents);
    public function getAllClientEvents();

    // public function getAllClientEvents($eventId);
    // public function getAllClientEventsGroupByRoles($eventId);
}

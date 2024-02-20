<?php

namespace App\Interfaces;

interface ClientEventRepositoryInterface
{
    public function getAllClientEventDataTables(array $filter);
    public function getAllClientEventByClientIdDataTables($clientId);
    public function getAllClientEventByClientId($clientId);
    public function getReportClientEventsGroupByRoles($eventId);
    public function getConversionLead($filter);
    public function getClientEventByClientId($clientId);
    public function getClientEventByTicketId($ticketId);
    public function getClientEventByClientIdAndEventId($clientId, $eventId);
    public function getClientEventById($clientEventId);
    public function getJoinedClientByEventId($eventId);
    public function getClientEventByEventId($eventId);
    # new
    public function getExistingMenteeFromClientEvent($eventId = null);
    public function getExistingNonMenteeFromClientEvent($eventId = null);
    public function getUndefinedClientFromClientEvent($eventId = null);

    public function deleteClientEvent($clientEventId);
    public function createClientEvent(array $clientEvents);
    public function updateClientEvent($clientEventId, array $clientEvents);
    public function getAllClientEvents();

    // public function getAllClientEvents($eventId);
    // public function getAllClientEventsGroupByRoles($eventId);
}

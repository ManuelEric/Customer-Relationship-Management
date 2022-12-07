<?php

namespace App\Interfaces;

interface EventRepositoryInterface 
{
    public function getAllEventDataTables();
    public function getAllEvents();
    public function getEventById($eventId);
    public function deleteEvent($eventId);
    public function createEvent(array $eventDetails);
    public function updateEvent($eventId, array $newDetails);
    public function addEventPic($eventId, $employeeId);
    public function updateEventPic($eventId, $employeeId);
}
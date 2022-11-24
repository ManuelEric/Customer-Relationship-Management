<?php

namespace App\Interfaces;

interface SchoolEventRepositoryInterface 
{
    public function getSchoolByEventId($eventId);
    public function addSchoolEvent($eventId, array $schoolDetails);
    public function destroySchoolEvent($eventId, $schoolId);
}
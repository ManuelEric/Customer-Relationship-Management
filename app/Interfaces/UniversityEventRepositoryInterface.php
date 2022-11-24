<?php

namespace App\Interfaces;

interface UniversityEventRepositoryInterface 
{
    public function getUniversityByEventId($eventId);
    public function addUniversityEvent($eventId, array $universityDetails);
    public function destroyUniversityEvent($eventId, $universityId);
}
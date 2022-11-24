<?php

namespace App\Repositories;

use App\Interfaces\UniversityEventRepositoryInterface;
use App\Models\Event;
use Illuminate\Support\Carbon;

class UniversityEventRepository implements UniversityEventRepositoryInterface 
{
    public function getUniversityByEventId($eventId)
    {
        $event = Event::whereEventId($eventId);
        return $event->university;
    }

    public function addUniversityEvent($eventId, $universityDetails)
    {
        $event = Event::whereEventId($eventId);
        return $event->university()->attach($universityDetails, [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public function destroyUniversityEvent($eventId, $universityId)
    {
        $event = Event::whereEventId($eventId);
        return $event->university()->detach($universityId);
    }
}
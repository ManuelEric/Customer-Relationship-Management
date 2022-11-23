<?php

namespace App\Repositories;

use App\Interfaces\SchoolEventRepositoryInterface;
use App\Models\Event;
use Illuminate\Support\Carbon;

class SchoolEventRepository implements SchoolEventRepositoryInterface 
{
    public function getSchoolByEventId($eventId)
    {
        $event = Event::whereEventId($eventId);
        return $event->school;
    }

    public function addSchoolEvent($eventId, $schoolDetails)
    {
        $event = Event::whereEventId($eventId);
        return $event->school()->attach($schoolDetails, [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public function destroySchoolEvent($eventId, $schoolId)
    {
        $event = Event::whereEventId($eventId);
        return $event->school()->detach($schoolId);
    }
}
<?php

namespace App\Repositories;

use App\Interfaces\EventRepositoryInterface;
use App\Models\Event;
use App\Models\User;
use DataTables;
use Illuminate\Support\Carbon;

class EventRepository implements EventRepositoryInterface 
{

    public function getAllEventDataTables()
    {
        return Datatables::eloquent(Event::query())->rawColumns(['event_description', 'event_location'])->make(true);
    }

    public function getAllEvents()
    {
        return Event::orderBy('created_at', 'desc')->get();
    }

    public function getEventById($eventId)
    {   
        return Event::whereEventId($eventId);
    }

    public function deleteEvent($eventId)
    {
        return Event::whereEventId($eventId)->delete();
    }

    public function createEvent(array $eventDetails)
    {
        return Event::create($eventDetails);
    }

    public function updateEvent($eventId, array $newDetails)
    {
        return Event::whereEventId($eventId)->update($newDetails);
    }

    public function addEventPic($eventId, $employeeId)
    {
        $event = Event::whereEventId($eventId);
        return $event->eventPic()->attach($employeeId, [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public function updateEventPic($eventId, $employeeId)
    {
        $event = Event::whereEventId($eventId);
        return $event->eventPic()->sync($employeeId, [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    # dashboard
    public function getEventsWithParticipants($cp_filter)
    {
        $userId = $this->getUser($cp_filter);

        return Event::withCount('clientEvent as participants')->whereHas('clientEvent', function ($query) use ($cp_filter) {
            $query->whereMonth('tbl_client_event.created_at', date('m', strtotime($cp_filter['qdate'])))
                    ->whereYear('tbl_client_event.created_at', date('Y', strtotime($cp_filter['qdate'])));
        })->when($userId, function($query) use ($userId) {
            $query->whereHas('eventPic', function ($query) use ($userId) {
                $query->where('empl_id', $userId);
            });
        })->get();
    }

    #

    private function getUser($cp_filter)
    {
        $userId = null;
        if (isset($cp_filter['quuid']) && $uuid = $cp_filter['quuid']) {
            $user = User::where('uuid', $uuid)->first();
            $userId = $user->id;
        }

        return $userId;
    }
}
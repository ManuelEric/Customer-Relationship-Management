<?php

namespace App\Repositories;

use App\Interfaces\EventRepositoryInterface;
use App\Models\Event;
use App\Models\User;
use DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

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

    public function getEventByName($eventName)
    {
        return Event::where('event_title', $eventName)->first();
    }

    public function getEventByMonthyear($monthyear)
    {
        return Event::whereMonth('event_startdate', date('m', strtotime($monthyear)))
                        ->whereYear('event_startdate', date('Y', strtotime($monthyear)))
                        ->get();
    }

    public function deleteEvent($eventId)
    {
        $event = Event::whereEventId($eventId);
        $eventBanner = $event->event_banner;

        if (!$event->delete()) 
            return false;

            # if there is event banner
            # also delete the event banner
            if ($eventBanner !== NULL) {
                $existingImagePath = storage_path('app/public/uploaded_file/events').'/'.$eventBanner;
                if (File::exists($existingImagePath))
                    File::delete($existingImagePath);
            }

        return true;
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
        $current_year = date('Y');
        $last_3_year = date('Y') - 2;

        return Event::withCount('clientEvent as participants')->whereHas('clientEvent', function ($query) use ($cp_filter, $current_year, $last_3_year) {

            $query->when($cp_filter['qyear'] == "last-3-year", function ($sq) use ($current_year, $last_3_year) {
                $sq->whereRaw('YEAR(tbl_client_event.created_at) BETWEEN ? AND ?', [$last_3_year, $current_year]);
                // $sq->whereYearBetween('tbl_client_event.created_at', [date('Y')-2, date('Y')]);
            }, function ($sq) use ($cp_filter) {
                $sq->whereYear('tbl_client_event.created_at', date('Y'));
            });
        })->when($userId, function ($query) use ($userId) {
            $query->whereHas('eventPic', function ($query) use ($userId) {
                $query->where('users.id', $userId);
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

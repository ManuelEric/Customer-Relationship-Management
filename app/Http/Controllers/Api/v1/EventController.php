<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\EventRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{

    protected EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function findEvent(Request $request)
    {
        $requested_event_id = $request->event_id;
        if (!$found_event = $this->eventRepository->getEventById($requested_event_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Could not find the event.'
            ]);
        }


        return response()->json([
            'success' => true,
            'message' => 'Event was found.',
            'data' => [
                'event_id' => $found_event->event_id,
                'event_name' => $found_event->event_title,
                'event_banner' => $found_event->event_banner !== null ? Storage::url("events/{$found_event->event_banner}") : null,
                'active_event' => $this->checkActiveEvent($found_event->event_startdate, $found_event->event_enddate)
            ]
        ]);
    }

    private function checkActiveEvent($start, $end)
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        $now = Carbon::now();

        if ($now->lte($start))
            return true;

        return $now->between($start, $end) || $now->lte(date: $start) ? true : false;

    }
}

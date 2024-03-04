<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\EventRepositoryInterface;
use Illuminate\Http\Request;

class ExtEventController extends Controller
{

    protected EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }
    
    public function getEvents(Request $request)
    {
        $events = $this->eventRepository->getAllEvents();
        if (!$events) {
            return response()->json([
                'success' => true,
                'message' => 'No event found.'
            ]);
        }

        # map the data that being shown to the user
        $mappedEvents = $events->map(function ($value) {
            return [
                'event_id' => $value->event_id,
                'event_title' => $value->event_title,
                'event_description' => $value->event_description,
                'event_location' => $value->event_location,
                'event_startdate' => $value->event_startdate,
                'event_enddate' => $value->event_enddate,
                'event_banner' => $value->event_banner
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'There are events available.',
            'data' => $mappedEvents
        ]);
    }
}

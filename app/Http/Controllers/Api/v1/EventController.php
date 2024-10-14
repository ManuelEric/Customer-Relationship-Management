<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\EventRepositoryInterface;
use Illuminate\Http\Request;

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
                'event_banner' => $found_event->event_banner !== null ? url("/storage/uploaded_file/events/{$found_event->event_banner}") : null
            ]
        ]);
    }
}

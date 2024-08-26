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
        $requestedEventId = $request->event_id;
        if (!$foundEvent = $this->eventRepository->getEventById($requestedEventId)) {
            return response()->json([
                'success' => false,
                'message' => 'Could not find the event.'
            ]);
        }


        return response()->json([
            'success' => true,
            'message' => 'Event was found.',
            'data' => [
                'event_id' => $foundEvent->event_id,
                'event_name' => $foundEvent->event_title,
                'event_banner' => $foundEvent->event_banner !== null ? url("/storage/uploaded_file/events/{$foundEvent->event_banner}") : null
            ]
        ]);
    }
}

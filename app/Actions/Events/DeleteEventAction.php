<?php

namespace App\Actions\Events;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\EventRepositoryInterface;

class DeleteEventAction
{
    use CreateCustomPrimaryKeyTrait;
    private EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function execute(
        String $event_id
    )
    {
        # Delete event
        $deleted_event = $this->eventRepository->deleteEvent($event_id);

        return $deleted_event;
    }
}
<?php

namespace App\Actions\Schools\Event;

use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Interfaces\SchoolEventRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;

class DeleteSchoolEventAction
{
    private SchoolEventRepositoryInterface $schoolEventRepository;
    private SchoolDetailRepositoryInterface $schoolDetailRepository;
    private EventRepositoryInterface $eventRepository;

    public function __construct(SchoolEventRepositoryInterface $schoolEventRepository, SchoolDetailRepositoryInterface $schoolDetailRepository, EventRepositoryInterface $eventRepository)
    {
        $this->schoolEventRepository = $schoolEventRepository;
        $this->schoolDetailRepository = $schoolDetailRepository;
        $this->eventRepository = $eventRepository;
    }

    public function execute(
        String $event_id,
        String $school_id
    )
    {
        $event = $this->eventRepository->getEventById($event_id);
        
        if (count($event->school_speaker()->where('sch_id', $school_id)->get()) > 0) {
            $this->schoolDetailRepository->deleteAgendaSpeaker($school_id, $event_id);
        }
        
        # Delete school event
        $deleted_school_event = $this->schoolEventRepository->destroySchoolEvent($event_id, $school_id);

        return $deleted_school_event;
    }
}
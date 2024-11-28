<?php

namespace App\Actions\Events\Speaker;

use App\Http\Traits\FindAgendaSpeakerPriorityTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;

class CreateEventSpeakerAction
{
    use FindAgendaSpeakerPriorityTrait;
    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;

    public function __construct(AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function execute(
        String $event_id,
        array $new_agenda_details
    ) {

        $new_agenda_details['event_id'] = $event_id;
        $new_agenda_details['priority'] = (int) $this->maxAgendaSpeakerPriority('Event', $event_id, $new_agenda_details)+1;

        $new_agenda = $this->agendaSpeakerRepository->createAgendaSpeaker("Event", $event_id, $new_agenda_details);

        return $new_agenda;
    }
}

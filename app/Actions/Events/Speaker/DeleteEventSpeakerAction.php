<?php

namespace App\Actions\Events\Speaker;

use App\Interfaces\AgendaSpeakerRepositoryInterface;

class DeleteEventSpeakerAction
{
    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;

    public function __construct(AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function execute(
        $agenda_id
    )
    {
        # Delete event speaker
        $deleted_event_speaker = $this->agendaSpeakerRepository->deleteAgendaSpeaker($agenda_id);

        return $deleted_event_speaker;
    }
}
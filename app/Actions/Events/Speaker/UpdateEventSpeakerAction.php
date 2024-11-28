<?php

namespace App\Actions\Events\Speaker;

use App\Http\Requests\StoreSpeakerRequest;
use App\Interfaces\AgendaSpeakerRepositoryInterface;

class UpdateEventSpeakerAction
{
    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepositoryInterface;

    public function __construct(AgendaSpeakerRepositoryInterface $agendaSpeakerRepositoryInterface)
    {
        $this->agendaSpeakerRepositoryInterface = $agendaSpeakerRepositoryInterface;
    }

    public function execute(
        StoreSpeakerRequest $request,
        Array $new_event_speaker_details
    ) {

        # Update event speaker
        $updated_event_speaker = $this->agendaSpeakerRepositoryInterface->updateAgendaSpeaker($request->speaker, $new_event_speaker_details);

        return $updated_event_speaker;
    }
}

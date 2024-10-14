<?php

namespace App\Actions\EdufLeads\Speaker;

use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;

class UpdateEdufLeadSpeakerAction
{
    use StandardizePhoneNumberTrait;
    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;

    public function __construct(AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function execute(
        Array $new_eduf_lead_speaker_details
    )
    {
       
        # Update eduf lead speaker
        $updated_eduf_lead_speaker = $this->agendaSpeakerRepository->updateAgendaSpeaker($new_eduf_lead_speaker_details['speaker'], ['status' => $new_eduf_lead_speaker_details['status_speaker'], 'notes' => $new_eduf_lead_speaker_details['notes_reason']]);

        return $updated_eduf_lead_speaker;
    }
}
<?php

namespace App\Actions\EdufLeads\Speaker;

use App\Http\Traits\FindAgendaSpeakerPriorityTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;

Class CreateEdufLeadSpeakerAction
{
    use FindAgendaSpeakerPriorityTrait, StandardizePhoneNumberTrait;
    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;

    public function __construct(AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function execute(
        $eduf_lead_id,
        Array $new_agenda_details
    )
    {

        # store new agenda
        $new_agenda_details['speaker_type'] = 'internal';
        $new_agenda_details['eduf_id'] = $eduf_lead_id;
        $new_agenda_details['priority'] = (int) $this->maxAgendaSpeakerPriority('Edufair', $eduf_lead_id, $new_agenda_details) + 1;

        
        $new_agenda = $this->agendaSpeakerRepository->createAgendaSpeaker("Edufair", $eduf_lead_id, $new_agenda_details);


        return $new_agenda;
    }
}
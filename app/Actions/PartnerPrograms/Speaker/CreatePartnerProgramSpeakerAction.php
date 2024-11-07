<?php

namespace App\Actions\PartnerPrograms\Speaker;

use App\Http\Traits\FindAgendaSpeakerPriorityTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;

class CreatePartnerProgramSpeakerAction
{
    use FindAgendaSpeakerPriorityTrait;
    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;

    public function __construct(AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function execute(
        Array $agenda_details,
        $partner_program_id,
    )
    {

        $agenda_details['partner_prog_id'] = $partner_program_id;
        $agenda_details['priority'] = (int) $this->maxAgendaSpeakerPriority('Partner-Program', $partner_program_id, $agenda_details)+1;

        # insert into partner program speaker
        $new_data_partner_program_speaker = $this->agendaSpeakerRepository->createAgendaSpeaker("Partner-Program", $partner_program_id, $agenda_details);

        return $new_data_partner_program_speaker;
    }
}
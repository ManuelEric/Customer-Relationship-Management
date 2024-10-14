<?php

namespace App\Actions\EdufLeads\Speaker;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;

class DeleteEdufLeadSpeakerAction
{
    use CreateCustomPrimaryKeyTrait;
    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;

    public function __construct(AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function execute(
        $agenda_id
    )
    {
        # Delete eduf_lead_speaker
        $deleted_eduf_lead_speaker = $this->agendaSpeakerRepository->deleteAgendaSpeaker($agenda_id);

        return $deleted_eduf_lead_speaker;
    }
}
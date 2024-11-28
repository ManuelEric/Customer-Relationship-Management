<?php

namespace App\Actions\PartnerPrograms\Speaker;

use App\Http\Traits\FindAgendaSpeakerPriorityTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;

class UpdatePartnerProgramSpeakerAction
{
    use FindAgendaSpeakerPriorityTrait;
    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;

    public function __construct(AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function execute(
        $agenda_id,
        $status,
        $notes
    )
    {

        $updated_speaker = $this->agendaSpeakerRepository->updateAgendaSpeaker($agenda_id, ['status' => $status, 'notes' => $notes]);

        return $updated_speaker;
    }
}
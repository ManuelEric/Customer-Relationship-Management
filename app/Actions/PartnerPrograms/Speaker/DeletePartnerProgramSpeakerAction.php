<?php

namespace App\Actions\PartnerPrograms\Speaker;

use App\Interfaces\AgendaSpeakerRepositoryInterface;

class DeletePartnerProgramSpeakerAction
{
    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;

    public function __construct(AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function execute(
        $agenda_id,
    )
    {

        $deleted_speaker = $this->agendaSpeakerRepository->deleteAgendaSpeaker($agenda_id);


        return $deleted_speaker;
    }
}
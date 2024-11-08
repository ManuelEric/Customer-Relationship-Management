<?php

namespace App\Actions\SchoolPrograms\Speaker;

use App\Http\Traits\FindAgendaSpeakerPriorityTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;

class CreateSchoolProgramSpeakerAction
{
    use FindAgendaSpeakerPriorityTrait;
    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;

    public function __construct(AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function execute(
        Array $agenda_details,
        $school_program_id,
    )
    {

        $agenda_details['sch_prog_id'] = $school_program_id;
        $agenda_details['priority'] = (int) $this->maxAgendaSpeakerPriority('School-Program', $school_program_id, $agenda_details) + 1;

        # insert into school program speaker
        $new_data_school_program_speaker = $this->agendaSpeakerRepository->createAgendaSpeaker("School-Program", $school_program_id, $agenda_details);

        return $new_data_school_program_speaker;
    }
}
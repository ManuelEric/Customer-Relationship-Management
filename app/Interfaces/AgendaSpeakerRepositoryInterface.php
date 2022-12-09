<?php

namespace App\Interfaces;

interface AgendaSpeakerRepositoryInterface 
{
    public function getAllSpeakerByMonthAndYear($month, $year);
    public function getAllSpeakerByEvent($eventId);
    public function getAllSpeakerBySchoolProgram($schProgId);
    public function getAllSpeakerByEventAndMonthAndYear($eventId, $month, $year);
    public function getAgendaSpeakerById($agendaId);
    public function deleteAgendaSpeaker($agendaId);
    public function createAgendaSpeaker($class, $identifier, array $agendaDetails);
    public function updateAgendaSpeaker($agendaId, array $newDetails);
}
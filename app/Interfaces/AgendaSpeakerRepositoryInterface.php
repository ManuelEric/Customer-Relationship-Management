<?php

namespace App\Interfaces;

interface AgendaSpeakerRepositoryInterface
{
    public function getAllSpeakerByMonthAndYear($month, $year);
    public function getAllSpeakerByEvent($eventId);
    public function getAllSpeakersByEventAndSchool($eventId, $schoolId);
    public function getAllSpeakerDashboard($type, $date = null);
    public function getAllSpeakerBySchoolProgram($schProgId);
    public function getAllSpeakerByPartnerProgram($partnerProgId);
    public function getAllSpeakerByEdufair($edufId);
    public function getAllSpeakerByEventAndMonthAndYear($eventId, $month, $year);
    public function getAgendaSpeakerById($agendaId);
    public function deleteAgendaSpeaker($agendaId);
    public function createAgendaSpeaker($class, $identifier, array $agendaDetails);
    public function updateAgendaSpeaker($agendaId, array $newDetails);
}

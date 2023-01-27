<?php

namespace App\Repositories;

use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Models\Agenda;
use App\Models\AgendaSpeaker;
use App\Models\Event;
use App\Models\SchoolProgram;
use DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class AgendaSpeakerRepository implements AgendaSpeakerRepositoryInterface
{
    public function getAllSpeakerByMonthAndYear($month, $year): JsonResponse
    {
        # month should be an integer (ex: 01, 02, 03, etc)
        # year (ex: 2016, 2017, etc)
        $agenda = AgendaSpeaker::whereMonth('event_startdate', $month)->whereYear('event_startdate', $year)->whereMonth('event_enddate', $month)->whereYear('event_enddate', $year)->get();
        return response()->json($agenda);
    }

    public function getAllSpeakerByEvent($eventId)
    {
        return Agenda::where('event_id', $eventId)->get();
    }

    public function getAllSpeakerBySchoolProgram($schProgId)
    {
        return Agenda::where('sch_prog_id', $schProgId)->get();
    }

    public function getAllSpeakerByPartnerProgram($partnerProgId)
    {
        return AgendaSpeaker::where('partner_prog_id', $partnerProgId)->get();
    }

    public function getAllSpeakerByEventAndMonthAndYear($eventId, $month, $year): JsonResponse
    {
        return response()->json();
    }

    public function getAgendaSpeakerById($agendaId)
    {
    }

    public function deleteAgendaSpeaker($agendaId)
    {
        return AgendaSpeaker::destroy($agendaId);
    }

    public function createAgendaSpeaker($class, $identifier, $agendaDetails)
    {
        switch ($class) {
            case "Event":
                return $this->createEventSpeaker($identifier, $agendaDetails);
                break;

            case "School-Program":
                return $this->createSchoolProgramSpeaker($identifier, $agendaDetails);
                break;

            case "Partner-Program":
                return $this->createPartnerProgramSpeaker($identifier, $agendaDetails);
                break;
        }
    }

    public function updateAgendaSpeaker($agendaId, array $newDetails)
    {
        return AgendaSpeaker::find($agendaId)->update($newDetails);
    }

    # event speaker below
    public function createEventSpeaker($identifier, $agendaDetails)
    {
        # initialize 
        $agendaDetails['created_at'] = Carbon::now();
        $agendaDetails['updated_at'] = Carbon::now();
        $event = Event::whereEventId($identifier);

        switch ($agendaDetails['speaker_type']) {

            case "school":
                return $event->school_speaker()->attach($agendaDetails['school_speaker'], $agendaDetails);
                break;

            case "university":

                $event->university_speaker()->attach($agendaDetails['university_speaker'], $agendaDetails);
                break;

            case "partner":

                $event->partner_speaker()->attach($agendaDetails['partner_speaker'], $agendaDetails);
                break;

            case "internal":

                $event->internal_speaker()->attach($agendaDetails['allin_speaker'], $agendaDetails);
                break;
        }
    }

    # school program speaker below
    public function createSchoolProgramSpeaker($identifier, $agendaDetails)
    {


        switch ($agendaDetails['speaker_type']) {

            case "school":
                $agendaDetails['sch_pic_id'] = $agendaDetails['school_speaker'];
                break;

            case "partner":
                $agendaDetails['partner_pic_id'] = $agendaDetails['partner_speaker'];
                break;

            case "internal":
                $agendaDetails['empl_id'] = $agendaDetails['allin_speaker'];
                break;
        }


        return AgendaSpeaker::create($agendaDetails);
    }

    # school program speaker below
    public function createPartnerProgramSpeaker($identifier, $agendaDetails)
    {


        switch ($agendaDetails['speaker_type']) {

            case "school":
                $agendaDetails['sch_pic_id'] = $agendaDetails['school_speaker'];
                break;

            case "partner":
                $agendaDetails['partner_pic_id'] = $agendaDetails['partner_speaker'];
                break;

            case "internal":
                $agendaDetails['empl_id'] = $agendaDetails['allin_speaker'];
                break;
        }


        return AgendaSpeaker::create($agendaDetails);
    }
}

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
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Switch_;

class AgendaSpeakerRepository implements AgendaSpeakerRepositoryInterface
{
    public function getAllSpeakerByMonthAndYear($month, $year): JsonResponse
    {
        # month should be an integer (ex: 01, 02, 03, etc)
        # year (ex: 2016, 2017, etc)
        $agenda = AgendaSpeaker::whereMonth('event_startdate', $month)->whereYear('event_startdate', $year)->whereMonth('event_enddate', $month)->whereYear('event_enddate', $year)->get();
        return response()->json($agenda);
    }

    public function getAllSpeakerDashboard($type, $date = null)
    {
        $agendaSpeaker =  AgendaSpeaker::leftJoin('tbl_sch_prog', 'tbl_agenda_speaker.sch_prog_id', '=', 'tbl_sch_prog.id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_agenda_speaker.partner_prog_id')
            ->leftJoin(
                'tbl_prog',
                'tbl_prog.prog_id',
                DB::raw('CASE
                        WHEN tbl_agenda_speaker.sch_prog_id > 0 THEN 
                            tbl_sch_prog.prog_id 
                        WHEN tbl_agenda_speaker.partner_prog_id > 0 THEN 
                            tbl_partner_prog.prog_id
                        ELSE null
                    END')
            )
            ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
            ->leftJoin('tbl_events', 'tbl_events.event_id', '=', 'tbl_agenda_speaker.event_id')
            ->leftJoin('tbl_eduf_lead', 'tbl_eduf_lead.id', '=', 'tbl_agenda_speaker.eduf_id')
            ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_eduf_lead.corp_id')
            ->leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_eduf_lead.sch_id')
            ->leftJoin('tbl_schdetail', 'tbl_schdetail.schdetail_id', '=', 'tbl_agenda_speaker.sch_pic_id')
            ->leftJoin('tbl_univ_pic', 'tbl_univ_pic.id', '=', 'tbl_agenda_speaker.univ_pic_id')
            ->leftJoin('tbl_corp_pic', 'tbl_corp_pic.id', '=', 'tbl_agenda_speaker.partner_pic_id')
            ->leftJoin('users', 'users.id', '=', 'tbl_agenda_speaker.empl_id')

            ->select(
                DB::raw(
                    '(CASE
                        WHEN tbl_agenda_speaker.event_id is not null THEN tbl_events.event_title
                        WHEN tbl_agenda_speaker.eduf_id is not null THEN 
                            (CASE 
                                WHEN tbl_eduf_lead.title IS NOT NULL THEN tbl_eduf_lead.title
                                ELSE 
                                    (CASE 
                                        WHEN tbl_eduf_lead.sch_id is NULL THEN CONCAT(tbl_corp.corp_name, " (", DATE_FORMAT(tbl_eduf_lead.created_at, "%e %b %Y"), ")")
                                        ELSE CONCAT(tbl_sch.sch_name, " (", DATE_FORMAT(tbl_eduf_lead.created_at, "%e %b %Y"), ")")
                                    END)
                            END)
                        WHEN tbl_agenda_speaker.sch_prog_id > 0 OR tbl_agenda_speaker.partner_prog_id > 0
                            THEN 
                                (CASE
                                WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                                    ELSE tbl_prog.prog_program
                                END) 
                    END) AS event_name'
                ),
                DB::raw('(CASE
                        WHEN tbl_agenda_speaker.sch_pic_id > 0 THEN tbl_schdetail.schdetail_fullname
                        WHEN tbl_agenda_speaker.partner_pic_id > 0 THEN tbl_corp_pic.pic_name
                        WHEN tbl_agenda_speaker.univ_pic_id > 0 THEN tbl_univ_pic.name
                        WHEN tbl_agenda_speaker.empl_id > 0 THEN CONCAT(users.first_name," ",users.last_name)
                        ELSE null
                    END) AS speaker_name'),
                'tbl_agenda_speaker.start_time',
                'tbl_agenda_speaker.end_time',
            )
            ->where('tbl_agenda_speaker.status', 1);

        switch ($type) {
            case "all":
                return $agendaSpeaker->get();
                break;
            case "byDate":
                return $agendaSpeaker
                    ->whereDate('tbl_agenda_speaker.start_time', '<=', $date)
                    ->whereDate('tbl_agenda_speaker.end_time', '>=', $date)
                    ->get();
                break;
        }
    }

    public function getAllSpeakerByEvent($eventId)
    {
        return Agenda::where('event_id', $eventId)->get();
    }

    public function getAllSpeakersByEventAndSchool($eventId, $schoolId)
    {
        return Agenda::where('event_id', $eventId)->where('school_id', $schoolId)->first();
    }

    public function getAllSpeakerBySchoolProgram($schProgId)
    {
        return Agenda::where('sch_prog_id', $schProgId)->get();
    }

    public function getAllSpeakerByPartnerProgram($partnerProgId)
    {
        return Agenda::where('partner_prog_id', $partnerProgId)->get();
    }

    public function getAllSpeakerByEdufair($edufId)
    {
        return Agenda::where('eduf_id', $edufId)->get();
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

            case "Edufair":
                return $this->createEdufairSpeaker($agendaDetails);
                break;
        }
    }

    public function updateAgendaSpeaker($agendaId, array $newDetails)
    {
        return tap(AgendaSpeaker::find($agendaId))->update($newDetails);
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

    public function createEdufairSpeaker($agendaDetails)
    {
        $agendaDetails['empl_id'] = $agendaDetails['speaker'];

        return AgendaSpeaker::create($agendaDetails);
    }
}

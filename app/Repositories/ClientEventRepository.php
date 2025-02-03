<?php

namespace App\Repositories;

use App\Interfaces\ClientEventRepositoryInterface;
use App\Models\ClientEvent;
use App\Models\ClientProgram;
use App\Models\School;
use App\Models\User;
use App\Models\UserClient;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ClientEventRepository implements ClientEventRepositoryInterface
{

    public function getAllClientEventDataTables($filter = [])
    {
        $query = ClientEvent::leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_event.client_id')
                ->leftJoin('tbl_client_roles', 'tbl_client_roles.client_id', '=', 'tbl_client.id')
                ->leftJoin('tbl_roles', 'tbl_roles.id', '=', 'tbl_client_roles.role_id')
                ->leftJoin('tbl_events', 'tbl_events.event_id', '=', 'tbl_client_event.event_id')
                ->leftJoin('tbl_lead', 'tbl_lead.lead_id', '=', 'tbl_client_event.lead_id')
                ->leftJoin('tbl_eduf_lead', 'tbl_eduf_lead.id', '=', 'tbl_client_event.eduf_id')
                ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_client_event.partner_id')
                ->leftJoin('tbl_corp as ceduf', 'ceduf.corp_id', '=', 'tbl_eduf_lead.corp_id')
                ->leftJoin('tbl_sch as seduf', 'seduf.sch_id', '=', 'tbl_eduf_lead.sch_id')
                ->leftJoin('tbl_client as child', 'child.id', '=', 'tbl_client_event.child_id')
                ->leftJoin('client_ref_code_view', 'client_ref_code_view.id', '=', DB::raw('SUBSTR(tbl_client_event.referral_code, 4)'))
                ->leftJoin('tbl_client as cref', 'cref.secondary_id', '=', 'tbl_client_event.referral_code')
                ->leftJoin('tbl_lead as cllead', 'cllead.lead_id', '=', 'tbl_client.lead_id')
                ->leftJoin('tbl_eduf_lead as cleduf', 'cleduf.id', '=', 'tbl_client.eduf_id')
                ->leftJoin('tbl_corp as clcorpeduf', 'clcorpeduf.corp_id', '=', 'cleduf.corp_id')
                ->leftJoin('tbl_sch as clseduf', 'clseduf.sch_id', '=', 'cleduf.sch_id')
                ->leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 
                            DB::raw('CASE 
                                    WHEN tbl_roles.role_name = "Parent" THEN child.sch_id
                                    WHEN tbl_roles.role_name != "Parent" THEN tbl_client.sch_id
                                END'))

                ->select(
                    'tbl_client_event.clientevent_id',
                    'tbl_client_event.ticket_id',
                    'tbl_client.id as client_id',
                    'tbl_events.event_title as event_name',
                    'tbl_client.register_by',
                    DB::raw('CONCAT (tbl_client.first_name, " ", COALESCE(tbl_client.last_name, "")) AS client_name'),                   
                    'tbl_client.mail as client_mail',
                    'tbl_client.phone as client_phone',
                    'tbl_events.event_id',
                    DB::raw('(CASE
                        WHEN tbl_client_event.registration_type = "PR" THEN "Pre-Registration"
                        WHEN tbl_client_event.registration_type = "OTS" THEN "On The Spot"
                    END) AS registration_type'),
                    DB::raw('(CASE
                        WHEN tbl_roles.role_name = "Parent" THEN CONCAT(child.first_name, " ", COALESCE(child.last_name, ""))
                        WHEN tbl_roles.role_name != "Parent" THEN "-"
                    END) AS child_name'),
                    DB::raw('(CASE
                        WHEN tbl_roles.role_name = "Parent" THEN child.mail 
                        WHEN tbl_roles.role_name != "Parent" THEN "-"
                    END) AS child_mail'),
                    DB::raw('(CASE
                        WHEN tbl_roles.role_name = "Parent" THEN child.phone 
                        WHEN tbl_roles.role_name != "Parent" THEN "-"
                    END) AS child_phone'),
                    DB::raw('(CASE
                        WHEN tbl_roles.role_name = "Parent" THEN (SELECT EXISTS(SELECT clientprog_id FROM tbl_client_prog cp WHERE cp.client_id = child.id))
                        WHEN tbl_roles.role_name != "Parent" THEN (SELECT EXISTS(SELECT clientprog_id FROM tbl_client_prog cp WHERE cp.client_id = tbl_client.id))
                    END) AS participated_program'),
                    DB::raw('(CASE
                        WHEN tbl_roles.role_name = "Parent" THEN (SELECT EXISTS(SELECT clientevent_id FROM tbl_client_event ce WHERE ce.client_id = child.id))
                        WHEN tbl_roles.role_name != "Parent" THEN (SELECT EXISTS(SELECT clientevent_id FROM tbl_client_event ce WHERE ce.client_id = tbl_client.id))
                    END) AS participated_event'),
                    DB::raw('(CASE
                        WHEN (SELECT participated_program) = 1 OR (SELECT participated_event) 
                            THEN "Yes" 
                            ELSE "No"
                    END) AS participated'),
                    DB::raw('(CASE
                        WHEN tbl_roles.role_name = "Parent" 
                            THEN (SELECT GROUP_CONCAT(
                                    ct.name
                                ) FROM tbl_client_abrcountry sqac
                                JOIN tbl_country ct ON ct.id = sqac.country_id
                                WHERE sqac.client_id = tbl_client.id GROUP BY sqac.client_id)
                        WHEN tbl_roles.role_name != "Parent" 
                            THEN (SELECT GROUP_CONCAT(
                                    ct.name
                                ) FROM tbl_client_abrcountry sqac
                                JOIN tbl_country ct ON ct.id = sqac.country_id
                                WHERE sqac.client_id = child.id GROUP BY sqac.client_id)
                    END) AS abr_country'),
                    'tbl_sch.sch_name AS school_name',
                    DB::raw('(CASE
                        WHEN tbl_roles.role_name = "Parent" THEN child.graduation_year_now
                        WHEN tbl_roles.role_name != "Parent" THEN tbl_client.graduation_year_now
                    END) AS graduation_year'),
                    DB::raw('(CASE
                        WHEN tbl_roles.role_name = "Parent" THEN child.grade_now
                        WHEN tbl_roles.role_name != "Parent" THEN tbl_client.grade_now 
                    END) AS grade_now'),
                    'tbl_client_event.joined_date',
                    'tbl_client_event.notes',
                    'tbl_client_event.status',
                    'tbl_client_event.created_at',
                    'tbl_client_event.number_of_attend as number_of_party',
                    'tbl_client.created_at as client_created_at',
                    DB::raw('(CASE
                        WHEN tbl_lead.main_lead = "KOL" THEN CONCAT(tbl_lead.sub_lead)
                        WHEN tbl_lead.main_lead = "External Edufair" THEN 
                            (CASE 
                                WHEN tbl_eduf_lead.title != null THEN CONCAT(tbl_eduf_lead.title) 
                                ELSE 
                                (CASE 
                                    WHEN tbl_eduf_lead.sch_id IS NULL THEN ceduf.corp_name 
                                    ELSE seduf.sch_name
                                END)
                            END)
                        WHEN tbl_lead.main_lead = "All-In Partners" THEN CONCAT(tbl_corp.corp_name)
                        ELSE tbl_lead.main_lead
                    END) AS conversion_lead'),
                    DB::raw('(CASE
                        WHEN cllead.main_lead = "KOL" THEN CONCAT(cllead.sub_lead)
                        WHEN cllead.main_lead = "External Edufair" THEN 
                            (CASE 
                                WHEN cleduf.title != null THEN CONCAT(cleduf.title) 
                                ELSE 
                                (CASE 
                                    WHEN cleduf.sch_id IS NULL THEN clcorpeduf.corp_name 
                                    ELSE clseduf.sch_name
                                END)
                            END)
                        WHEN cllead.main_lead = "All-In Partners" THEN cllead.main_lead
                        ELSE cllead.main_lead
                    END) AS lead_source'),
                    DB::raw('CONCAT (cref.first_name, " ", COALESCE(cref.last_name, "")) AS referral_name'),
                )->
                when(!empty($filter['audience']), function ($searchQuery) use ($filter) {
                    $searchQuery->whereIn('tbl_client.register_as', $filter['audience']);
                })->
                when(!empty($filter['event_name']), function ($searchQuery) use ($filter) {
                    $searchQuery->where('event_title', $filter['event_name']);
                })->
                when(!empty($filter['school_name']), function ($searchQuery) use ($filter) {
                    $searchQuery->whereIn(DB::raw('tbl_sch.sch_name'), $filter['school_name']);
                })->
                when(!empty($filter['graduation_year']), function ($searchQuery) use ($filter) {
                    $searchQuery->whereIn(DB::raw('(CASE
                            WHEN tbl_roles.role_name = "Parent" THEN child.graduation_year_now
                            WHEN tbl_roles.role_name != "Parent" THEN tbl_client.graduation_year_now
                        END)'), $filter['graduation_year']);
                })->
                when(!empty($filter['conversion_lead']), function ($searchQuery) use ($filter) {
                    $searchQuery->whereIn('tbl_lead.lead_id', $filter['conversion_lead']);
                })->
                when($filter && isset($filter['attendance']), function ($searchQuery) use ($filter) {
                    $searchQuery->where('tbl_client_event.status', $filter['attendance']);
                })->
                when(!empty($filter['registration']), function ($searchQuery) use ($filter) {
                    $searchQuery->where('tbl_client_event.registration_type', $filter['registration']);
                })->
                when(!empty($filter['start_date']) && !empty($filter['end_date']), function ($searchQuery) use ($filter) {
                    $searchQuery->whereBetween('joined_date', [$filter['start_date'], $filter['end_date']]);
                })->
                when(!empty($filter['start_date']) && empty($filter['end_date']), function ($searchQuery) use ($filter) {
                    $searchQuery->where('joined_date', '>=', $filter['start_date']);
                })->
                when(empty($filter['start_date']) && !empty($filter['end_date']), function ($searchQuery) use ($filter) {
                    $searchQuery->where('joined_date', '<=', $filter['end_date']);
                })->
                groupBy('tbl_client_event.clientevent_id');

            return DataTables::eloquent($query)->
            filterColumn(
                'conversion_lead',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN tbl_lead.main_lead = "KOL" THEN CONCAT(tbl_lead.sub_lead)
                                WHEN tbl_lead.main_lead = "External Edufair" THEN 
                                    (CASE 
                                        WHEN tbl_eduf_lead.title != null THEN CONCAT(tbl_eduf_lead.title) 
                                        ELSE 
                                        (CASE 
                                            WHEN tbl_eduf_lead.sch_id IS NULL THEN ceduf.corp_name 
                                            ELSE seduf.sch_name
                                        END)
                                    END)
                                WHEN tbl_lead.main_lead = "All-In Partners" THEN CONCAT(tbl_corp.corp_name)
                                ELSE tbl_lead.main_lead
                            END) like ? ';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn(
                'lead_source',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN cllead.main_lead = "KOL" THEN CONCAT(cllead.sub_lead)
                                WHEN cllead.main_lead = "External Edufair" THEN 
                                    (CASE 
                                        WHEN cleduf.title != null THEN CONCAT(cleduf.title) 
                                        ELSE 
                                        (CASE 
                                            WHEN cleduf.sch_id IS NULL THEN clcorpeduf.corp_name 
                                            ELSE clseduf.sch_name
                                        END)
                                    END)
                                WHEN cllead.main_lead = "All-In Partners" THEN cllead.main_lead
                                ELSE cllead.main_lead
                            END) like ? ';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn(
                'status',
                function ($query, $keyword) {
                    $sql = '(CASE 
                            WHEN tbl_client_event.status = 0 THEN "Join"
                            WHEN tbl_client_event.status = 1 THEN "Attend"
                        END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn(
                'participated',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN tbl_roles.role_name = "Parent" THEN (SELECT checkParticipated (child.id))
                                WHEN tbl_roles.role_name != "Parent" THEN (SELECT checkParticipated (tbl_client.id))
                            END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn(
                'client_name',
                function ($query, $keyword) {
                    $sql = 'CONCAT (tbl_client.first_name, " ", COALESCE(tbl_client.last_name, "")) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn(
                'child_name',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN tbl_roles.role_name = "Parent" THEN CONCAT(child.first_name, " ", COALESCE(child.last_name, ""))
                                WHEN tbl_roles.role_name != "Parent" THEN "-" 
                            END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn(
                'graduation_year',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN tbl_roles.role_name = "Parent" THEN child.graduation_year_now
                                WHEN tbl_roles.role_name != "Parent" THEN tbl_client.graduation_year_now 
                            END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn(
                'grade_now',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN tbl_roles.role_name = "Parent" THEN child.grade_now
                                WHEN tbl_roles.role_name != "Parent" THEN tbl_client.grade_now 
                            END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn(
                'abr_country',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN tbl_roles.role_name = "Parent" 
                                    THEN (SELECT GROUP_CONCAT(
                                            ct.name
                                        ) FROM tbl_client_abrcountry sqac
                                        JOIN tbl_country ct ON ct.id = sqac.country_id
                                        WHERE sqac.client_id = tbl_client.id GROUP BY sqac.client_id)
                                WHEN tbl_roles.role_name != "Parent" 
                                    THEN (SELECT GROUP_CONCAT(
                                            ct.name
                                        ) FROM tbl_client_abrcountry sqac
                                        JOIN tbl_country ct ON ct.id = sqac.country_id
                                        WHERE sqac.client_id = child.id GROUP BY sqac.client_id)
                            END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn('referral_name', function ($query, $keyword) {
                $sql = 'CONCAT (cref.first_name, " ", COALESCE(cref.last_name, "")) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->make(true);
    }

    public function getAllClientEventByClientIdDataTables($clientId)
    {
        return datatables::eloquent(
            ClientEvent::leftJoin('tbl_client', 'tbl_client.id', '=', 
                        DB::raw('CASE 
                            WHEN tbl_client_event.child_id is not null THEN tbl_client_event.child_id
                            ELSE tbl_client_event.client_id
                        END'))
                ->leftJoin('tbl_events', 'tbl_events.event_id', '=', 'tbl_client_event.event_id')
                ->leftJoin('tbl_lead', 'tbl_lead.lead_id', '=', 'tbl_client_event.lead_id')
                ->leftJoin('tbl_eduf_lead', 'tbl_eduf_lead.id', '=', 'tbl_client_event.eduf_id')
                ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_client_event.partner_id')
                ->where(
                    DB::raw('CASE 
                        WHEN tbl_client_event.child_id is not null THEN tbl_client_event.child_id
                        ELSE tbl_client_event.client_id
                    END'), 
                    '=', 
                    $clientId)
                ->select(
                    'tbl_client_event.clientevent_id',
                    // 'tbl_client_event.event_id',
                    // 'tbl_client_event.eduf_id',
                    'tbl_events.event_title as event_name',
                    // 'tbl_lead.main_lead',
                    'tbl_client_event.joined_date',
                    'tbl_client_event.status',
                    'tbl_events.event_startdate',
                    DB::raw('(CASE
                    WHEN tbl_lead.main_lead = "KOL" THEN CONCAT("KOL: ", tbl_lead.sub_lead)
                    WHEN tbl_lead.main_lead = "External Edufair" THEN CONCAT(tbl_eduf_lead.title)
                    WHEN tbl_lead.main_lead = "All-In Partners" THEN CONCAT(tbl_corp.corp_name)
                    ELSE tbl_lead.main_lead
                END) AS conversion_lead'),
                    DB::raw('CONCAT(tbl_client.first_name," ", COALESCE(tbl_client.last_name, "")) as client_name')
                )
        )->make(true);
    }

    public function getAllClientEventByClientId($clientId)
    {
        return ClientEvent::where('client_id', $clientId)->get();
    }

    public function getReportClientEventsGroupByRoles($eventId = null)
    {
        return ClientEvent::leftJoin('tbl_client', 'tbl_client.id', '=', DB::raw('(CASE WHEN tbl_client_event.child_id is null THEN tbl_client_event.client_id ELSE tbl_client_event.child_id END)'))
            ->leftJoin('tbl_client_roles', 'tbl_client_roles.client_id', '=', 'tbl_client.id')
            ->leftJoin('tbl_roles', 'tbl_roles.id', '=', 'tbl_client_roles.role_id')
            ->leftJoin('tbl_client_prog', 'tbl_client_prog.client_id', '=', 'tbl_client.id')
            ->leftJoin('program', 'program.prog_id', '=', 'tbl_client_prog.prog_id')
            ->select(
                'tbl_client.register_by',
                'tbl_client_event.clientevent_id',
                DB::raw('(CASE 
                    WHEN tbl_client_event.child_id is null THEN tbl_client_event.client_id 
                    ELSE tbl_client_event.child_id 
                END) as client_id'),
                'tbl_client_event.created_at',
                'tbl_client_event.joined_date',
                'program.main_prog_id',
                'tbl_roles.role_name',
                'tbl_client_prog.status',
            )->
            when(isset($eventId), function ($subQuery) use ($eventId) {
                $subQuery->where('tbl_client_event.event_id', $eventId);
            })->
            get();   
    }

    public function getConversionLead($filter = null)
    {
        // return $filter;
        $eventId = isset($filter['eventId']) ? $filter['eventId'] : null;
        $userId = $this->getUser($filter);
        // $year = $filter['qyear'];

        $current_year = date('Y');
        $last_3_year = date('Y') - 2;

        return ClientEvent::leftJoin('tbl_lead', 'tbl_lead.lead_id', '=', 'tbl_client_event.lead_id')
            ->leftJoin('tbl_eduf_lead', 'tbl_eduf_lead.id', '=', 'tbl_client_event.eduf_id')
            ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_client_event.partner_id')
            ->leftJoin('tbl_corp as ceduf', 'ceduf.corp_id', '=', 'tbl_eduf_lead.corp_id')
            ->leftJoin('tbl_sch as seduf', 'seduf.sch_id', '=', 'tbl_eduf_lead.sch_id')
            ->select(
                DB::raw('(CASE
                WHEN tbl_lead.main_lead = "KOL" THEN CONCAT("KOL: ", tbl_lead.sub_lead)
                WHEN tbl_lead.main_lead = "External Edufair" THEN (CASE WHEN tbl_eduf_lead.title != null THEN CONCAT(tbl_eduf_lead.title) ELSE (CASE WHEN tbl_eduf_lead.sch_id IS NULL THEN ceduf.corp_name ELSE seduf.sch_name END)END)
                WHEN tbl_lead.main_lead = "All-In Partners" THEN CONCAT("All-In Partners: ", tbl_corp.corp_name)
                ELSE tbl_lead.main_lead
            END) AS conversion_lead'),
                DB::raw('COUNT((CASE
                WHEN tbl_lead.main_lead = "KOL" THEN CONCAT(tbl_lead.sub_lead)
                WHEN tbl_lead.main_lead = "External Edufair" THEN (CASE WHEN tbl_eduf_lead.title != null THEN CONCAT(tbl_eduf_lead.title) ELSE (CASE WHEN tbl_eduf_lead.sch_id IS NULL THEN ceduf.corp_name ELSE seduf.sch_name END)END)
                WHEN tbl_lead.main_lead = "All-In Partners" THEN CONCAT(tbl_corp.corp_name)
                ELSE tbl_lead.main_lead
            END)) AS count_conversionLead'),
            )
            ->groupBy('conversion_lead')
            ->when(
                isset($eventId) && !isset($userId) && !isset($filter['qyear']),
                function ($query) use ($eventId) {
                    $query->where('tbl_client_event.event_id', $eventId);
                }
            )
            ->when(
                !isset($eventId) && !isset($userId) && !isset($filter['qyear']),
                function ($query) {
                    $query->whereMonth('tbl_client_event.created_at', date('m'))
                        ->whereYear('tbl_client_event.created_at', date('Y'));
                }
            )
            ->when($userId, function ($query) use ($userId) {
                $query->whereHas('event', function ($q) use ($userId) {
                    $q->whereHas('eventPic', function ($q2) use ($userId) {
                        $q2->where('users.id', $userId);
                    });
                });
            })->when(
                isset($filter['qyear']) && $filter['qyear'] == "last-3-year" && !$eventId,
                function ($sq) use ($current_year, $last_3_year) {
                    $sq->whereRaw('YEAR(tbl_client_event.created_at) BETWEEN ? AND ?', [$last_3_year, $current_year]);
                    // $sq->whereYearBetween('tbl_client_event.created_at', [date('Y')-2, date('Y')]);
                }
            )->when(
                isset($filter['qyear']) && !$eventId,
                function ($sq) {
                    $sq->whereYear('tbl_client_event.created_at', date('Y'));
                }
            )
            ->get();
    }

    public function getClientEventByClientId($clientId)
    {
        return ClientEvent::where('client_id', $clientId)->first();
    }

    public function getClientEventByTicketId($ticketId)
    {
        return ClientEvent::where('ticket_id', $ticketId)->first();
    }

    public function getClientEventByClientIdAndEventId($clientId, $eventId)
    {
        return ClientEvent::where('client_id', $clientId)->where('event_id', $eventId)->first();
    }

    public function getClientEventByMultipleIdAndEventId($clientId, $eventId, $secondClient=null)
    {
        return ClientEvent::when($secondClient !== NULL, function($query) use($clientId, $secondClient) {
            $query->where('client_id', $clientId)->where('child_id', $secondClient);
        }, function ($query) use ($clientId) {
            $query->where('client_id', $clientId);
        })->where('event_id', $eventId)->first();  
    }

    public function getClientEventById($clientEventId)
    {
        return ClientEvent::find($clientEventId);
    }

    public function getClientEventByEventId($eventId)
    {
        return ClientEvent::where('event_id', $eventId)->get();
    }

    public function getJoinedClientByEventId($eventId)
    {
        return ClientEvent::where('event_id', $eventId)->where('status', 1)->
            where(function ($query) {


                $query->whereDoesntHave('logMail', function($subQuery) {
                    $subQuery->where('category', 'thanks-mail-after');
                })->
                
                orWhereHas('logMail', function ($subQuery) {
                    $subQuery->where('sent_status', 0)->where('category', 'thanks-mail-after');
                });

                
            })->
            get();
    }

    # new 
    public function getExistingMenteeFromClientEvent($eventId = null)
    {
        return ClientEvent::
            leftJoin('tbl_client', 'tbl_client.id', '=', DB::raw('(CASE WHEN tbl_client_event.child_id is null THEN tbl_client_event.client_id ELSE tbl_client_event.child_id END)'))
            ->leftJoin('tbl_client_roles', 'tbl_client_roles.client_id', '=', 'tbl_client.id')
            ->leftJoin('tbl_roles', 'tbl_roles.id', '=', 'tbl_client_roles.role_id')
            ->leftJoin('tbl_client_prog', 'tbl_client_prog.client_id', '=', 'tbl_client.id')
            ->leftJoin('program', 'program.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_client as parent', 'parent.id', '=', 
                DB::raw('( SELECT
                    MAX(parent_id) parent_id
                    FROM tbl_client_relation as relation
                    WHERE relation.child_id = tbl_client.id
                )'))
            ->select(
                'parent.id as parent_id',
                'parent.mail as parent_mail',
                'parent.phone as parent_phone',
                'tbl_client.register_by',
                'tbl_client_event.clientevent_id',
                DB::raw('(CASE 
                    WHEN tbl_client_event.child_id is null THEN tbl_client_event.client_id 
                    ELSE tbl_client_event.child_id 
                END) as client_id'),
                'tbl_client_event.created_at',
                'tbl_client_event.joined_date',
                'program.main_prog_id',
                'tbl_roles.role_name',
                'tbl_client_prog.status',
            )->
            selectRaw('RTRIM(CONCAT(parent.first_name, " ", COALESCE(parent.last_name, ""))) as parent_name')->
            when(isset($eventId), function ($subQuery) use ($eventId) {
                $subQuery->where('tbl_client_event.event_id', $eventId);
            })->
            where('role_name', 'Mentee')->groupBy('client_id')->get();
    }
    
    public function getExistingNonMenteeFromClientEvent($eventId = null)
    {
        $id_existingMentee = $this->getExistingMenteeFromClientEvent($eventId)->pluck('client_id')->toArray();

        return ClientEvent::
            leftJoin('tbl_client', 'tbl_client.id', '=', DB::raw('(CASE WHEN tbl_client_event.child_id is null THEN tbl_client_event.client_id ELSE tbl_client_event.child_id END)'))
            ->leftJoin('tbl_client_roles', 'tbl_client_roles.client_id', '=', 'tbl_client.id')
            ->leftJoin('tbl_roles', 'tbl_roles.id', '=', 'tbl_client_roles.role_id')
            ->leftJoin('tbl_client_prog', 'tbl_client_prog.client_id', '=', 'tbl_client.id')
            ->leftJoin('program', 'program.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_client as parent', 'parent.id', '=', 
                DB::raw('( SELECT
                    MAX(parent_id) parent_id
                    FROM tbl_client_relation as relation
                    WHERE relation.child_id = tbl_client.id
                )'))
            ->select(
                'parent.id as parent_id',
                'parent.mail as parent_mail',
                'parent.phone as parent_phone',
                'tbl_client.register_by',
                'tbl_client_event.clientevent_id',
                DB::raw('(CASE 
                    WHEN tbl_client_event.child_id is null THEN tbl_client_event.client_id 
                    ELSE tbl_client_event.child_id 
                END) as client_id'),
                'tbl_client_event.created_at',
                'tbl_client_event.joined_date',
                'program.main_prog_id',
                'tbl_roles.role_name',
                'tbl_client_prog.status',
            )->
            selectRaw('RTRIM(CONCAT(parent.first_name, " ", COALESCE(parent.last_name, ""))) as parent_name')->
            when(isset($eventId), function ($subQuery) use ($eventId) {
                $subQuery->where('tbl_client_event.event_id', $eventId);
            })->
            where('role_name', '!=', 'Mentee')->
            where('tbl_client_prog.status', 1)->
            where('main_prog_id', '!=', 1)->
            whereNotIn(DB::raw('(CASE 
            WHEN tbl_client_event.child_id is null THEN tbl_client_event.client_id 
            ELSE tbl_client_event.child_id 
        END)'), $id_existingMentee)->groupBy('client_id')->get();
    }

    public function getUndefinedClientFromClientEvent($eventId = null)
    {
        $id_existingMentee = $this->getExistingMenteeFromClientEvent($eventId)->pluck('client_id')->toArray();
        $id_existingNonMentee = $this->getExistingNonMenteeFromClientEvent($eventId)->pluck('client_id')->toArray();
        $ids = array_merge($id_existingMentee, $id_existingNonMentee);

        return ClientEvent::
            leftJoin('tbl_client', 'tbl_client.id', '=', DB::raw('(CASE WHEN tbl_client_event.child_id is null THEN tbl_client_event.client_id ELSE tbl_client_event.child_id END)'))
            ->leftJoin('tbl_client_roles', 'tbl_client_roles.client_id', '=', 'tbl_client.id')
            ->leftJoin('tbl_roles', 'tbl_roles.id', '=', 'tbl_client_roles.role_id')
            ->leftJoin('tbl_client_prog', 'tbl_client_prog.client_id', '=', 'tbl_client.id')
            ->leftJoin('program', 'program.prog_id', '=', 'tbl_client_prog.prog_id')
            ->select(
                'tbl_client.register_by',
                'tbl_client_event.clientevent_id',
                DB::raw('(CASE 
                    WHEN tbl_client_event.child_id is null THEN tbl_client_event.client_id 
                    ELSE tbl_client_event.child_id 
                END) as client_id'),
                'tbl_client_event.created_at',
                'tbl_client_event.joined_date',
                'program.main_prog_id',
                'tbl_roles.role_name',
                'tbl_client_prog.status',
            )->
            when(isset($eventId), function ($subQuery) use ($eventId) {
                $subQuery->where('tbl_client_event.event_id', $eventId);
            })->
            whereNotIn('client_id', $ids)->groupBy('client_id')->get();
    }

    public function deleteClientEvent($clientEventId)
    {
        return ClientEvent::destroy($clientEventId);
    }

    public function createClientEvent(array $clientEvents)
    {
        return ClientEvent::create($clientEvents);
    }

    public function updateClientEvent($clientEventId, array $newClientEvents)
    {
        $clientEvent = ClientEvent::find($clientEventId);
        $clientEvent->update($newClientEvents);

        return $clientEvent;

    }

    public function updateClientEvents(array $clientEventIds, array $newClientEvents)
    {
        $clientEvent = ClientEvent::whereIn($clientEventIds);
        $clientEvent->update($newClientEvents);

        return $clientEvent;

    }

    # 

    private function getUser($cp_filter)
    {
        $userId = null;
        if (isset($cp_filter['quuid']) && $cp_filter['quuid'] !== null) {
            $uuid = $cp_filter['quuid'];
            $user = User::where('uuid', $uuid)->first();
            $userId = $user->id;
        }

        return $userId;
    }

    public function getAllClientEvents()
    {
        return ClientEvent::all();
    }

    public function isTicketIDUnique($ticketId)
    {
        return ClientEvent::where('ticket_id', $ticketId)->exists() ? false : true;
    }
}

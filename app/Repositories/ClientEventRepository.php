<?php

namespace App\Repositories;

use App\Interfaces\ClientEventRepositoryInterface;
use App\Models\ClientEvent;
use App\Models\User;
use DataTables;
use Illuminate\Support\Facades\DB;

class ClientEventRepository implements ClientEventRepositoryInterface
{

    public function getAllClientEventDataTables($filter = [])
    {
        $query = ClientEvent::leftJoin('client', 'client.id', '=', 'tbl_client_event.client_id')
                ->leftJoin('tbl_events', 'tbl_events.event_id', '=', 'tbl_client_event.event_id')
                ->leftJoin('tbl_lead', 'tbl_lead.lead_id', '=', 'tbl_client_event.lead_id')
                ->leftJoin('tbl_eduf_lead', 'tbl_eduf_lead.id', '=', 'tbl_client_event.eduf_id')
                ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_client_event.partner_id')
                ->leftJoin('tbl_corp as ceduf', 'ceduf.corp_id', '=', 'tbl_eduf_lead.corp_id')
                ->leftJoin('tbl_sch as seduf', 'seduf.sch_id', '=', 'tbl_eduf_lead.sch_id')
                // ->leftJoin('tbl_client_relation', 'tbl_client_relation.parent_id', '=', 'client.id')
                ->leftJoin('client as child', 'child.id', '=', 'tbl_client_event.child_id')
                ->select(
                    'tbl_client_event.clientevent_id',
                    // 'tbl_client_event.event_id',
                    // 'tbl_client_event.eduf_id',
                    'tbl_events.event_title as event_name',
                    'tbl_client_event.notes',
                    'client.register_as',
                    'client.full_name as client_name',
                    'client.mail as client_mail',
                    'client.phone as client_phone',
                    'client.abr_country as abr_country',
                    DB::raw('(CASE
                        WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN "-"
                        WHEN client.register_as = "parent" THEN child.full_name 
                    END) AS child_name'),
                    DB::raw('(CASE
                        WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN client.participated
                        WHEN client.register_as = "parent" THEN child.participated 
                    END) AS participated'),
                    DB::raw('(CASE
                        WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN client.school_name
                        WHEN client.register_as = "parent" THEN child.school_name 
                    END) AS school_name'),
                    DB::raw('(CASE
                        WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN client.graduation_year_real
                        WHEN client.register_as = "parent" THEN child.graduation_year_real 
                    END) AS graduation_year'),
                    'tbl_client_event.joined_date',
                    'tbl_client_event.status',
                    'tbl_client_event.created_at',
                    'tbl_client_event.number_of_attend as number_of_party',
                    'client.created_at as client_created_at',
                    DB::raw('(CASE
                        WHEN tbl_lead.main_lead = "KOL" THEN CONCAT(tbl_lead.sub_lead)
                        WHEN tbl_lead.main_lead = "External Edufair" THEN 
                            (CASE 
                                WHEN tbl_eduf_lead.title COLLATE utf8mb4_unicode_ci != null THEN CONCAT(tbl_eduf_lead.title COLLATE utf8mb4_unicode_ci) 
                                ELSE 
                                (CASE 
                                    WHEN tbl_eduf_lead.sch_id IS NULL THEN ceduf.corp_name COLLATE utf8mb4_unicode_ci 
                                    ELSE seduf.sch_name COLLATE utf8mb4_unicode_ci
                                END)
                            END)
                        WHEN tbl_lead.main_lead = "All-In Partners" THEN CONCAT(tbl_corp.corp_name COLLATE utf8mb4_unicode_ci)
                        WHEN tbl_lead.main_lead = "Referral" THEN CONCAT("Ref: ", (SELECT CONCAT(first_name, " ", last_name) FROM client_ref_code_view WHERE ref_code = tbl_client_event.referral_code))
                        ELSE tbl_lead.main_lead
                    END) AS conversion_lead'),
                )->
                when(!empty($filter['event_name']), function ($searchQuery) use ($filter) {
                    $searchQuery->where('event_title', $filter['event_name']);
                })->
                orderBy('tbl_client_event.created_at', 'DESC');

        return DataTables::eloquent($query)->
            filterColumn(
                'conversion_lead',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN tbl_lead.main_lead = "KOL" THEN CONCAT(tbl_lead.sub_lead)
                                WHEN tbl_lead.main_lead = "External Edufair" THEN CONCAT(tbl_eduf_lead.title COLLATE utf8mb4_unicode_ci)
                                WHEN tbl_lead.main_lead = "All-In Partners" THEN CONCAT(tbl_corp.corp_name COLLATE utf8mb4_unicode_ci)
                                ELSE tbl_lead.main_lead
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
                                WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN client.participated COLLATE utf8mb4_general_ci
                                WHEN client.register_as = "parent" THEN child.participated COLLATE utf8mb4_general_ci
                            END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn(
                'child_name',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN "-"
                                WHEN client.register_as = "parent" THEN child.full_name 
                            END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn(
                'school_name',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN client.school_name
                                WHEN client.register_as = "parent" THEN child.school_name 
                            END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn(
                'graduation_year',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN client.graduation_year_real
                                WHEN client.register_as = "parent" THEN child.graduation_year_real 
                            END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->make(true);
    }

    public function getAllClientEventByClientIdDataTables($clientId)
    {
        return datatables::eloquent(
            ClientEvent::leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_event.client_id')
                ->leftJoin('tbl_events', 'tbl_events.event_id', '=', 'tbl_client_event.event_id')
                ->leftJoin('tbl_lead', 'tbl_lead.lead_id', '=', 'tbl_client_event.lead_id')
                ->leftJoin('tbl_eduf_lead', 'tbl_eduf_lead.id', '=', 'tbl_client_event.eduf_id')
                ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_client_event.partner_id')
                ->where('tbl_client_event.client_id', '=', $clientId)
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

    public function getReportClientEvents($eventId = null)
    {
        $clientEvent = ClientEvent::leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_event.client_id')
            ->leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_client.sch_id')
            ->leftJoin('tbl_events', 'tbl_events.event_id', '=', 'tbl_client_event.event_id')
            ->leftJoin('tbl_lead', 'tbl_lead.lead_id', '=', 'tbl_client_event.lead_id')
            ->leftJoin('tbl_eduf_lead', 'tbl_eduf_lead.id', '=', 'tbl_client_event.eduf_id')
            ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_client_event.partner_id')
            ->select(
                'tbl_client.id as client_id',
                DB::raw('CONCAT(tbl_client.first_name," ", COALESCE(tbl_client.last_name, "")) as client_name'),
                'tbl_client.mail',
                'tbl_client.phone',
                'tbl_sch.sch_name',
                'tbl_client.st_grade',
                DB::raw('(CASE
                    WHEN tbl_lead.main_lead = "KOL" THEN CONCAT("KOL: ", tbl_lead.sub_lead)
                    WHEN tbl_lead.main_lead = "External Edufair" THEN CONCAT(tbl_eduf_lead.title)
                    WHEN tbl_lead.main_lead = "All-In Partners" THEN CONCAT(tbl_corp.corp_name)
                    ELSE tbl_lead.main_lead
                END) AS conversion_lead'),
                'tbl_client_event.joined_date',
                'tbl_client_event.client_id',
                'tbl_client_event.clientevent_id',
                'tbl_events.event_title',
                DB::raw(isset($eventId) ? "'ByEvent' as filter" : "'ByMonth' as filter"),
            );

        if (isset($eventId)) {
            return $clientEvent->where('tbl_client_event.event_id', $eventId)->get();
        } else {
            return $clientEvent->whereMonth('tbl_client_event.created_at', date('m'))->whereYear('tbl_client_event.created_at', date('Y'))->get();
        }
    }

    public function getReportClientEventsDataTables($eventId = null)
    {
        $clientEvent = ClientEvent::leftJoin('client', 'client.id', '=', 'tbl_client_event.client_id')
            ->leftJoin('tbl_events', 'tbl_events.event_id', '=', 'tbl_client_event.event_id')
            ->leftJoin('tbl_lead', 'tbl_lead.lead_id', '=', 'tbl_client_event.lead_id')
            ->leftJoin('tbl_eduf_lead', 'tbl_eduf_lead.id', '=', 'tbl_client_event.eduf_id')
            ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_client_event.partner_id')
            ->leftJoin('tbl_corp as ceduf', 'ceduf.corp_id', '=', 'tbl_eduf_lead.corp_id')
            ->leftJoin('tbl_sch as seduf', 'seduf.sch_id', '=', 'tbl_eduf_lead.sch_id')
            // ->leftJoin('tbl_client_relation', 'tbl_client_relation.child_id', '=', 'client.id')
            ->leftJoin('client as child', 'child.id', '=', 'tbl_client_event.child_id')

            ->select(
                'client.full_name as client_name',
                DB::raw('(CASE
                        WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN "-"
                        WHEN client.register_as = "parent" THEN child.full_name 
                    END) AS child_name'),
                // DB::raw('CONCAT(parent.first_name," ", COALESCE(parent.last_name, "")) as parent_name'),
                'client.mail',
                'client.phone',
                DB::raw('(CASE
                    WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN client.school_name
                    WHEN client.register_as = "parent" THEN child.school_name 
                END) AS school_name'),
                DB::raw('(CASE
                    WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN client.graduation_year_real
                    WHEN client.register_as = "parent" THEN child.graduation_year_real 
                END) AS graduation_year'),
                DB::raw('(CASE
                    WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN client.grade_now
                    WHEN client.register_as = "parent" THEN child.grade_now 
                END) AS grade_now'),
                // 'child.grade_now',
                // 'client.graduation_year_real',
                'client.lead_source',
                'client.abr_country',
                'tbl_client_event.joined_date',
                'tbl_events.event_title',
                'tbl_events.event_id',
                DB::raw('(CASE
                    WHEN tbl_lead.main_lead = "KOL" THEN CONCAT(tbl_lead.sub_lead)
                    WHEN tbl_lead.main_lead = "External Edufair" THEN (CASE WHEN tbl_eduf_lead.title != null THEN CONCAT(tbl_eduf_lead.title) ELSE (CASE WHEN tbl_eduf_lead.sch_id IS NULL THEN ceduf.corp_name ELSE seduf.sch_name END)END)
                    WHEN tbl_lead.main_lead = "All-In Partners" THEN CONCAT(tbl_corp.corp_name)
                    WHEN tbl_lead.main_lead = "Referral" THEN CONCAT("Ref: ", (SELECT CONCAT(first_name, " ", last_name) FROM client_ref_code_view WHERE ref_code = tbl_client_event.referral_code))
                    ELSE tbl_lead.main_lead
                END) AS conversion_lead'),
                DB::raw(isset($eventId) ? "'ByEvent' as filter" : "'ByMonth' as filter"),
            );

        if (isset($eventId)) {
            $clientEvent->where('tbl_client_event.event_id', $eventId);
        } else {
            $clientEvent->whereMonth('tbl_client_event.created_at', date('m'))->whereYear('tbl_client_event.created_at', date('Y'));
        }

        return datatables::eloquent($clientEvent)
            ->filterColumn(
                'conversion_lead',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN tbl_lead.main_lead COLLATE utf8mb4_unicode_ci = "KOL" THEN CONCAT(tbl_lead.sub_lead)
                                WHEN tbl_lead.main_lead COLLATE utf8mb4_unicode_ci = "External Edufair" THEN CONCAT(tbl_eduf_lead.title)
                                WHEN tbl_lead.main_lead COLLATE utf8mb4_unicode_ci = "All-In Partners" THEN CONCAT(tbl_corp.corp_name)
                                ELSE tbl_lead.main_lead COLLATE utf8mb4_unicode_ci
                            END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn(
                'school_name',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN client.school_name
                                WHEN client.register_as = "parent" THEN child.school_name 
                            END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn(
                'graduation_year',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN client.graduation_year_real
                                WHEN client.register_as = "parent" THEN child.graduation_year_real 
                            END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn(
                'grade_now',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN client.grade_now
                                WHEN client.register_as = "parent" THEN child.grade_now 
                            END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->
            filterColumn(
                'child_name',
                function ($query, $keyword) {
                    $sql = '(CASE
                                WHEN client.register_as = "student" OR client.register_as = "teacher/counsellor" THEN "-"
                                WHEN client.register_as = "parent" THEN child.full_name 
                            END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )
            ->make(true);
    }

    public function getReportClientEventsGroupByRoles($eventId = null)
    {
        $clientEvent = ClientEvent::leftJoin('tbl_client', 'tbl_client.id', '=', DB::raw('(CASE WHEN tbl_client_event.child_id is null THEN tbl_client_event.client_id ELSE tbl_client_event.child_id END)'))
            ->leftJoin('tbl_client_roles', 'tbl_client_roles.client_id', '=', 'tbl_client.id')
            ->leftJoin('tbl_roles', 'tbl_roles.id', '=', 'tbl_client_roles.role_id')
            ->leftJoin('tbl_client_prog', 'tbl_client_prog.client_id', '=', 'tbl_client.id')
            ->leftJoin('program', 'program.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_inv', 'tbl_inv.clientprog_id', '=', 'tbl_client_prog.clientprog_id')
            ->leftJoin('tbl_receipt', 'tbl_receipt.inv_id', '=', 'tbl_inv.inv_id')
            ->select(
                'tbl_client.register_as',
                'tbl_client_event.clientevent_id',
                // 'tbl_client_event.client_id',
                DB::raw('(CASE 
                    WHEN tbl_client_event.child_id is null THEN tbl_client_event.client_id 
                    ELSE tbl_client_event.child_id 
                END) as client_id'),
                'tbl_client_event.created_at',
                'tbl_client_event.joined_date',
                'program.main_prog_id',
                'tbl_roles.role_name',
                'tbl_client_prog.status',
                'tbl_receipt.id as id_receipt'
            );

        if (isset($eventId)) {
            return $clientEvent
                ->where('tbl_client_event.event_id', $eventId)
                ->get();
        } else {
            return $clientEvent
                ->whereMonth('tbl_client_event.created_at', date('m'))->whereYear('tbl_client_event.created_at', date('Y'))
                ->get();
        }
        // if (isset($eventId)) {
        //     return ClientEvent::leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_event.client_id')
        //         ->leftJoin('tbl_client_roles', 'tbl_client_roles.client_id', '=', 'tbl_client.id')
        //         ->leftJoin('tbl_roles', 'tbl_roles.id', '=', 'tbl_client_roles.role_id')
        //         ->select(
        //             'tbl_roles.role_name',
        //             DB::raw('count(role_id) as count_role')
        //         )
        //         ->groupBy('role_name')
        //         ->where('tbl_client_event.event_id', $eventId)
        //         ->get();
        // } else {
        //     return ClientEvent::leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_event.client_id')
        //         ->leftJoin('tbl_client_roles', 'tbl_client_roles.client_id', '=', 'tbl_client.id')
        //         ->leftJoin('tbl_roles', 'tbl_roles.id', '=', 'tbl_client_roles.role_id')
        //         ->select(
        //             'tbl_roles.role_name',
        //             DB::raw('count(role_id) as count_role')
        //         )
        //         ->groupBy('role_name')
        //         ->whereMonth('tbl_client_event.created_at', date('m'))->whereYear('tbl_client_event.created_at', date('Y'))
        //         ->get();
        // }
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

    public function getClientEventById($clientEventId)
    {
        return ClientEvent::find($clientEventId);
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
        return ClientEvent::find($clientEventId)->update($newClientEvents);
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
}

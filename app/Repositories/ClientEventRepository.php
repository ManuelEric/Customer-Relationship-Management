<?php

namespace App\Repositories;

use App\Interfaces\ClientEventRepositoryInterface;
use App\Models\ClientEvent;
use DataTables;
use Illuminate\Support\Facades\DB;

class ClientEventRepository implements ClientEventRepositoryInterface
{

    public function getAllClientEventDataTables()
    {
        return datatables::eloquent(
            ClientEvent::leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_event.client_id')->leftJoin('tbl_events', 'tbl_events.event_id', '=', 'tbl_client_event.event_id')->leftJoin('tbl_lead', 'tbl_lead.lead_id', '=', 'tbl_client_event.lead_id')->leftJoin('tbl_eduf_lead', 'tbl_eduf_lead.id', '=', 'tbl_client_event.eduf_id')->select(
                'tbl_client_event.clientevent_id',
                // 'tbl_client_event.event_id',
                // 'tbl_client_event.eduf_id',
                'tbl_lead.main_lead',
                'tbl_client_event.joined_date',
                'tbl_client_event.status',
                DB::raw('(CASE
                    WHEN tbl_lead.main_lead = "KOL" THEN CONCAT("KOL - ", tbl_lead.sub_lead)
                    WHEN tbl_lead.main_lead = "External Edufair" THEN CONCAT("External Edufair - ", tbl_eduf_lead.title)
                    WHEN tbl_lead.main_lead = "All-In Event" THEN CONCAT("All-In Event - ", tbl_events.event_title)
                    ELSE tbl_lead.main_lead
                END) AS event_name'),
                DB::raw('CONCAT(tbl_client.first_name," ", COALESCE(tbl_client.last_name, "")) as client_name')
            )
        )->filterColumn(
            'client_name',
            function ($query, $keyword) {
                $sql = 'CONCAT(tbl_client.first_name," ", COALESCE(tbl_client.last_name, "")) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )->filterColumn(
            'event_name',
            function ($query, $keyword) {
                $sql = '(CASE
                            WHEN tbl_lead.main_lead = "KOL" THEN CONCAT("KOL - ", tbl_lead.sub_lead)
                            WHEN tbl_lead.main_lead = "External Edufair" THEN CONCAT("External Edufair - ", tbl_eduf_lead.title)
                            WHEN tbl_lead.main_lead = "All-In Event" THEN CONCAT("All-In Event - ", tbl_events.event_title)
                            ELSE tbl_lead.main_lead
                        END) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )->make(true);
    }

    public function getAllClientEventByClientId($clientId)
    {
        return ClientEvent::where('client_id', $clientId)->get();
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
}
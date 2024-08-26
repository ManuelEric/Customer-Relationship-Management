<?php

namespace App\Repositories;

use App\Interfaces\EdufLeadRepositoryInterface;
use App\Models\EdufLead;
use App\Models\v1\Eduf as V1Eduf;
use Illuminate\Support\Facades\DB;
use DataTables;

class EdufLeadRepository implements EdufLeadRepositoryInterface
{
    public function getAllEdufairLeadDataTables()
    {
        return Datatables::eloquent(
            EdufLead::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_eduf_lead.corp_id')->
                leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_eduf_lead.sch_id')->
                leftJoin('users', 'users.id', '=', 'tbl_eduf_lead.intr_pic')->
                select([
                    'tbl_eduf_lead.*', 'tbl_sch.*',
                    DB::raw("IF (tbl_eduf_lead.sch_id IS NULL, tbl_corp.corp_name, tbl_sch.sch_name) as organizer_name"),
                    DB::raw("CONCAT(users.first_name, ' ', users.last_name) AS fullname")
                ])
        )->filterColumn(
            'organizer_name',
            function ($query, $keyword) {
                $sql = 'IF (tbl_eduf_lead.sch_id IS NULL, tbl_corp.corp_name, tbl_sch.sch_name) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )->filterColumn(
            'fullname',
            function ($query, $keyword) {
                $sql = 'CONCAT(users.first_name, " ", users.last_name) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )->make(true);
    }

    public function getAllEdufairLead()
    {
        return EdufLead::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_eduf_lead.corp_id')->
            leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_eduf_lead.sch_id')->
            leftJoin('users', 'users.id', '=', 'tbl_eduf_lead.intr_pic')->
            select([
                'tbl_eduf_lead.id', 'tbl_eduf_lead.location', 'tbl_eduf_lead.intr_pic',
                'tbl_eduf_lead.ext_pic_name', 'tbl_eduf_lead.ext_pic_mail', 'tbl_eduf_lead.ext_pic_phone', 'tbl_eduf_lead.first_discussion_date',
                'tbl_eduf_lead.last_discussion_date', 'tbl_eduf_lead.event_start', 'tbl_eduf_lead.event_end', 'tbl_eduf_lead.status', 'tbl_eduf_lead.notes',
                'tbl_eduf_lead.created_at', 'tbl_eduf_lead.updated_at', 'tbl_eduf_lead.sch_id', 'tbl_eduf_lead.corp_id',
                DB::raw("IF (tbl_eduf_lead.sch_id IS NOT NULL, tbl_sch.sch_name, tbl_corp.corp_name) as organizer_name")
            ])->orderBy('organizer_name', 'asc')->
            get();
    }

    public function getEdufairLeadById($edufLId)
    {
        return EdufLead::find($edufLId);
    }

    public function deleteEdufairLead($edufLId)
    {
        return EdufLead::whereId($edufLId)->delete();
    }

    public function createEdufairLead(array $edufairLeadDetails)
    {
        return EdufLead::create($edufairLeadDetails);
    }

    public function createEdufairLeads(array $edufairLeadDetails)
    {
        return EdufLead::insert($edufairLeadDetails);
    }

    public function updateEdufairLead($edufLId, array $newDetails)
    {
        return EdufLead::whereId($edufLId)->update($newDetails);
    }
    
    public function getEdufairLeadByYear($year)
    {
        return EdufLead::whereYear('event_start', $year)->get();
    }

    # CRM
    public function getAllEdufFromCRM()
    {
        return V1Eduf::all();
    }
}

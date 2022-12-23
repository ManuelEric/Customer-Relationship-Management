<?php

namespace App\Repositories;

use App\Interfaces\EdufLeadRepositoryInterface;
use App\Models\EdufLead;
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
            )->make(true);
    }

    public function getAllEdufairLead()
    {
        return EdufLead::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_eduf_lead.corp_id')->
                        leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_eduf_lead.sch_id')->
                        leftJoin('users', 'users.id', '=', 'tbl_eduf_lead.intr_pic')->
                        select(['tbl_eduf_lead.*', DB::raw("IF (tbl_eduf_lead.sch_id != NULL, tbl_sch.sch_name, tbl_corp.corp_name) as organizer_name")])->
                        orderBy('organizer_name', 'asc')->get();
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

    public function updateEdufairLead($edufLId, array $newDetails)
    {
        return EdufLead::whereId($edufLId)->update($newDetails);
    }
}
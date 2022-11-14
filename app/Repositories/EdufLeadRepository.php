<?php

namespace App\Repositories;

use App\Interfaces\EdufLeadRepositoryInterface;
use App\Models\EdufLead;

class EdufLeadRepository implements EdufLeadRepositoryInterface 
{
    public function getAllEdufairLeadDataTables()
    {
        return Datatables::eloquent(EdufLead::query())->make(true);
    }

    public function getAllEdufairLead()
    {
        return EdufLead::orderBy('eduf_organizer', 'asc')->get();
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
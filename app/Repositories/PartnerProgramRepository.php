<?php

namespace App\Repositories;

use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Models\PartnerProg;
use DataTables;

class PartnerProgramRepository implements PartnerProgramRepositoryInterface
{


    public function getAllPartnerProgramsByPartnerId($corpId)
    {
        return PartnerProg::where('corp_id', $corpId)->orderBy('id', 'asc')->get();
    }

    public function getPartnerProgramById($partnerProgId)
    {
        return PartnerProg::find($partnerProgId);
    }

    public function deletePartnerProgram($partnerProgId)
    {
        return PartnerProg::destroy($partnerProgId);
    }

    public function createPartnerProgram(array $partnerPrograms)
    {
        return PartnerProg::create($partnerPrograms);
    }

    public function updatePartnerProgram($partnerProgId, array $newPrograms)
    {
        return PartnerProg::find($partnerProgId)->update($newPrograms);
    }
}

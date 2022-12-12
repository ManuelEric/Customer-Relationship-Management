<?php

namespace App\Repositories;

use App\Interfaces\PartnerProgramAttachRepositoryInterface;
use App\Models\PartnerProgAttach;
use DataTables;

class PartnerProgramAttachRepository implements PartnerProgramAttachRepositoryInterface
{


    public function getAllPartnerProgramAttachsByPartnerProgId($partnerProgramId)
    {
        return PartnerProgAttach::where('partner_prog_id', $partnerProgramId)->orderBy('id', 'asc')->get();
    }

    public function getPartnerProgramAttachById($corpProgAttachId)
    {
        return PartnerProgAttach::find($corpProgAttachId);
    }

    public function deletePartnerProgramAttach($corpProgAttachId)
    {
        return PartnerProgAttach::destroy($corpProgAttachId);
    }

    public function createPartnerProgramAttach(array $partnerProgramAttachs)
    {
        return PartnerProgAttach::create($partnerProgramAttachs);
    }

    public function updatePartnerProgramAttach($corpProgAttachId, array $partnerProgramAttachs)
    {
        return PartnerProgAttach::find($corpProgAttachId)->update($partnerProgramAttachs);
    }
}

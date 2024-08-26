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

    public function getPartnerProgAttByPartnerProgIdnFileName($partnerProgId, $file_name)
    {
        return PartnerProgAttach::where('partner_prog_id', $partnerProgId)
            ->where('corprog_file', $file_name)->first();
    }

    public function deletePartnerProgramAttach($corpProgAttachId)
    {
        return PartnerProgAttach::destroy($corpProgAttachId);
    }

    public function createPartnerProgramAttach(array $partnerProgramAttachs)
    {
        return PartnerProgAttach::create($partnerProgramAttachs);
    }

    public function createPartnerProgramAttachs(array $partnerProgramAttachs)
    {
        return PartnerProgAttach::insert($partnerProgramAttachs);
    }

    public function updatePartnerProgramAttach($corpProgAttachId, array $partnerProgramAttachs)
    {
        return PartnerProgAttach::find($corpProgAttachId)->update($partnerProgramAttachs);
    }
}

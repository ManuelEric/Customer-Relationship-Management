<?php

namespace App\Actions\SchoolPrograms\Attach;

use App\Http\Traits\StoreAttachmentProgramTrait;
use App\Interfaces\SchoolProgramAttachRepositoryInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DeleteSchoolProgramAttachAction
{
    use StoreAttachmentProgramTrait;
    private SchoolProgramAttachRepositoryInterface $schoolProgramAttachRepository;

    public function __construct(SchoolProgramAttachRepositoryInterface $schoolProgramAttachRepository)
    {
        $this->schoolProgramAttachRepository = $schoolProgramAttachRepository;
    }

    public function execute(
        $attach_id
    )
    {

        $school_prog_attach = $this->schoolProgramAttachRepository->getSchoolProgramAttachById($attach_id);
        if (Storage::disk('s3')->exists('project/crm/attachment/sch_prog_attach/'. $school_prog_attach->schprog_id . '/' . $school_prog_attach->schprog_attach)) {

            if ($this->schoolProgramAttachRepository->deleteSchoolProgramAttach($attach_id)) {
                Storage::disk('s3')->delete('project/crm/attachment/sch_prog_attach/'. $school_prog_attach->schprog_id . '/' . $school_prog_attach->schprog_attach);
            }
        } else {
            $this->schoolProgramAttachRepository->deleteSchoolProgramAttach($attach_id);
        }

    }
}
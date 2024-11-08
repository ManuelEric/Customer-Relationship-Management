<?php

namespace App\Actions\SchoolPrograms\Attach;

use App\Http\Traits\StoreAttachmentProgramTrait;
use App\Interfaces\SchoolProgramAttachRepositoryInterface;
use Illuminate\Support\Facades\File;

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
        if (File::exists(public_path($school_prog_attach->schprog_attach))) {

            if ($this->schoolProgramAttachRepository->deleteSchoolProgramAttach($attach_id)) {
                Unlink(public_path($school_prog_attach->schprog_attach));
            }
        } else {
            $this->schoolProgramAttachRepository->deleteSchoolProgramAttach($attach_id);
        }

    }
}
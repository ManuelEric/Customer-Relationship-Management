<?php

namespace App\Actions\SchoolPrograms\Attach;

use App\Http\Requests\StoreSchoolProgramAttachRequest;
use App\Http\Traits\StoreAttachmentProgramTrait;
use App\Interfaces\SchoolProgramAttachRepositoryInterface;

class CreateSchoolProgramAttachAction
{
    use StoreAttachmentProgramTrait;
    private SchoolProgramAttachRepositoryInterface $schoolProgramAttachRepository;

    public function __construct(SchoolProgramAttachRepositoryInterface $schoolProgramAttachRepository)
    {
        $this->schoolProgramAttachRepository = $schoolProgramAttachRepository;
    }

    public function execute(
        StoreSchoolProgramAttachRequest $request,
        $school_program_id,
    )
    {

        $sch_prog_attach_details = $request->all();
        $sch_prog_attach_details['schprog_id'] = $school_program_id;

        $schprog_file =  $this->getFileNameAttachment($sch_prog_attach_details['schprog_file']);

        $schprog_attach = $this->attachmentProgram($request->file('schprog_attach'), $school_program_id, $schprog_file);


        $sch_prog_attach_details['schprog_file'] = $schprog_file;
        $sch_prog_attach_details['schprog_attach'] = $schprog_attach;

        # insert into school program attachment
        $new_data_school_program_attach = $this->schoolProgramAttachRepository->createSchoolProgramAttach($sch_prog_attach_details);

        return $new_data_school_program_attach;
    }
}
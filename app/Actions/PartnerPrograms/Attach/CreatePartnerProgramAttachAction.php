<?php

namespace App\Actions\PartnerPrograms\Attach;

use App\Http\Requests\StorePartnerProgramAttachRequest;
use App\Http\Traits\StoreAttachmentProgramTrait;
use App\Interfaces\PartnerProgramAttachRepositoryInterface;

class CreatePartnerProgramAttachAction
{
    use StoreAttachmentProgramTrait;
    private PartnerProgramAttachRepositoryInterface $partnerProgramAttachRepository;

    public function __construct(PartnerProgramAttachRepositoryInterface $partnerProgramAttachRepository)
    {
        $this->partnerProgramAttachRepository = $partnerProgramAttachRepository;
    }

    public function execute(
        StorePartnerProgramAttachRequest $request,
        $partner_program_id,
    )
    {

        $partner_program_attach_details = $request->all();
        $partner_program_attach_details['partner_prog_id'] = $partner_program_id;

        $corprog_file = $this->getFileNameAttachment($partner_program_attach_details['corprog_file']);

        $corprog_attach = $this->attachmentProgram($request->file('corprog_attach'), $partner_program_id, $corprog_file, 'partner_program');


        $partner_program_attach_details['corprog_file'] = $corprog_file;
        $partner_program_attach_details['corprog_attach'] = $corprog_attach;

        # store new partner program attach
        $new_data_partner_program_attach = $this->partnerProgramAttachRepository->createPartnerProgramAttach($partner_program_attach_details);

        return $new_data_partner_program_attach;
    }
}
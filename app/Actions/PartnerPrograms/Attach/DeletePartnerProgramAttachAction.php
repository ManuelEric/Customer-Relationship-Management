<?php

namespace App\Actions\PartnerPrograms\Attach;

use App\Http\Traits\StoreAttachmentProgramTrait;
use App\Interfaces\PartnerProgramAttachRepositoryInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DeletePartnerProgramAttachAction
{
    use StoreAttachmentProgramTrait;
    private PartnerProgramAttachRepositoryInterface $partnerProgramAttachRepository;

    public function __construct(PartnerProgramAttachRepositoryInterface $partnerProgramAttachRepository)
    {
        $this->partnerProgramAttachRepository = $partnerProgramAttachRepository;
    }

    public function execute(
        $attach_id
    )
    {

        $partner_prog_attach = $this->partnerProgramAttachRepository->getPartnerProgramAttachById($attach_id);
        if (Storage::disk('s3')->exists('project/crm/attachment/partner_prog_attach/'. $partner_prog_attach->partner_prog_id . '/' . $partner_prog_attach->corprog_attach)) {

            if ($this->partnerProgramAttachRepository->deletePartnerProgramAttach($attach_id)) {
                Storage::disk('s3')->delete('project/crm/attachment/partner_prog_attach/'. $partner_prog_attach->partner_prog_id . '/' . $partner_prog_attach->corprog_attach);
            }
        } else {
            $this->partnerProgramAttachRepository->deletePartnerProgramAttach($attach_id);
        }

    }
}
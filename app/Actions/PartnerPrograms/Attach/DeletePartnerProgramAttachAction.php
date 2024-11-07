<?php

namespace App\Actions\PartnerPrograms\Attach;

use App\Http\Traits\StoreAttachmentProgramTrait;
use App\Interfaces\PartnerProgramAttachRepositoryInterface;
use Illuminate\Support\Facades\File;

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
        if (File::exists(public_path($partner_prog_attach->corprog_attach))) {

            if ($this->partnerProgramAttachRepository->deletePartnerProgramAttach($attach_id)) {
                Unlink(public_path($partner_prog_attach->corprog_attach));
            }
        } else {
            $this->partnerProgramAttachRepository->deletePartnerProgramAttach($attach_id);
        }

    }
}
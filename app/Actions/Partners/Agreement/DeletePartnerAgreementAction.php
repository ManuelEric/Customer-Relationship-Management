<?php

namespace App\Actions\Partners\Agreement;

use App\Interfaces\PartnerAgreementRepositoryInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DeletePartnerAgreementAction
{
    private PartnerAgreementRepositoryInterface $partnerAgreementRepository;

    public function __construct(PartnerAgreementRepositoryInterface $partnerAgreementRepository)
    {
        $this->partnerAgreementRepository = $partnerAgreementRepository;
    }

    public function execute(
        $partner_agreement_id,
        $corp_id
    ) {
        $partner_agreement_attach = $this->partnerAgreementRepository->getPartnerAgreementById($partner_agreement_id);

        $file_path = 'project/crm/attachment/partner_agreement/' . $corp_id . '/' . $partner_agreement_attach->attachment;
        if (Storage::disk('s3')->exists($file_path)) {
            Storage::disk('s3')->delete($file_path);
        } else {
            $this->partnerAgreementRepository->deletePartnerAgreement($partner_agreement_id);
        }

        return $partner_agreement_attach;
    }
}

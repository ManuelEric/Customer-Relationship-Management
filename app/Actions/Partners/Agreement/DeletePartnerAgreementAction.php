<?php

namespace App\Actions\Partners\Agreement;

use App\Interfaces\PartnerAgreementRepositoryInterface;
use Illuminate\Support\Facades\File;

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
    )
    {
        $partner_agreement_attach = $this->partnerAgreementRepository->getPartnerAgreementById($partner_agreement_id);
    
            if(File::exists(public_path('attachment/partner_agreement/'. $corp_id . '/' . $partner_agreement_attach->attachment))){
                
                if($this->partnerAgreementRepository->deletePartnerAgreement($partner_agreement_id)){
                    Unlink(public_path('attachment/partner_agreement/'. $corp_id .'/' . $partner_agreement_attach->attachment));
                }
            }else{
                $this->partnerAgreementRepository->deletePartnerAgreement($partner_agreement_id);
            }

        return $partner_agreement_attach;
    }
}
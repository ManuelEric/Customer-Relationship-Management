<?php

namespace App\Actions\Partners\Agreement;

use App\Http\Requests\StorePartnerAgreementRequest;
use App\Http\Traits\UploadFileTrait;
use App\Interfaces\PartnerAgreementRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CreatePartnerAgreementAction
{
    use UploadFileTrait;
    private PartnerAgreementRepositoryInterface $partnerAgreementRepository;

    public function __construct(PartnerAgreementRepositoryInterface $partnerAgreementRepository)
    {
        $this->partnerAgreementRepository = $partnerAgreementRepository;
    }

    public function execute(
        StorePartnerAgreementRequest $request,
        String $corp_id,
        Array $partner_agreement_details
    )
    {

        $partner_agreement_details['corp_id'] = $corp_id;

        $file = $request->file('attachment');
        $file_name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), "_").'_'.Str::slug(Carbon::now(),"_");
        $extension = $file->getClientOriginalExtension();
        $file_location = 'attachment/partner_agreement/'.strtolower($corp_id).'/'; 
        $attachment = $file_name.'.'.$extension;
        
      
        $this->tnUploadFile($request, 'attachment', $file_name, $file_location);

        $partner_agreement_details['attachment'] = $attachment;

        # insert into partner aggrement
        $created_partner_agreement = $this->partnerAgreementRepository->createPartnerAgreement($partner_agreement_details);

        return $created_partner_agreement;
    }
}
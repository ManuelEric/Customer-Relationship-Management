<?php

namespace App\Actions\Partners\Agreement;

use App\Http\Requests\StorePartnerAgreementRequest;
use App\Http\Traits\UploadFileTrait;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\PartnerAgreementRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreatePartnerAgreementAction
{
    use UploadFileTrait;
    private PartnerAgreementRepositoryInterface $partnerAgreementRepository;
    private CorporateRepositoryInterface $corporateRepository;

    public function __construct(PartnerAgreementRepositoryInterface $partnerAgreementRepository, CorporateRepositoryInterface $corporateRepository)
    {
        $this->partnerAgreementRepository = $partnerAgreementRepository;
        $this->corporateRepository = $corporateRepository;
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
        $file_location = 'project/crm/attachment/partner_agreement/'.strtolower($corp_id).'/'; 
        $attachment = $file_name.'.'.$extension;

        Storage::disk('s3')->put($file_location . $attachment, file_get_contents($file));
      
        $partner_agreement_details['attachment'] = $attachment;

        # insert into partner aggrement
        $created_partner_agreement = $this->partnerAgreementRepository->createPartnerAgreement($partner_agreement_details);

        # update status partner to contracted
        if(isset($created_partner_agreement)){
            $this->corporateRepository->updateCorporate($created_partner_agreement->corp_id, ['corp_status' => 'Contracted']);
        }

        return $created_partner_agreement;
    }
}
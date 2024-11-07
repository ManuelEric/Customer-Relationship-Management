<?php

namespace App\Actions\ReferralPrograms;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\ReferralRepositoryInterface;
use App\Services\Program\ReferralProgramService;

class UpdateReferralProgramAction
{
    use CreateCustomPrimaryKeyTrait;
    private ReferralRepositoryInterface $referralRepository;
    private ReferralProgramService $referralProgramService;

    public function __construct(ReferralRepositoryInterface $referralRepository, ReferralProgramService $referralProgramService)
    {
        $this->referralRepository = $referralRepository;
        $this->referralProgramService = $referralProgramService;
    }

    public function execute(
        $referral_id,
        Array $referral_details,
    )
    {

       # Update attribute revenue by currency
       $referral_details_updated_attribute_revenue = $this->referralProgramService->snUpdateAttributeRevenueByCurrency($referral_details);

       # Update attribute program by referral type
       $referral_details_updated_attribute_program = $this->referralProgramService->snUpdateAttributeProgramByReferralType($referral_details_updated_attribute_revenue);

       $updated_referral_program = $this->referralRepository->updateReferral($referral_id, $referral_details_updated_attribute_program);
       return $updated_referral_program;
    }
}
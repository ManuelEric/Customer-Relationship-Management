<?php

namespace App\Actions\ReferralPrograms;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\ReferralRepositoryInterface;
use App\Services\Program\ReferralProgramService;

class CreateReferralProgramAction
{
    use CreateCustomPrimaryKeyTrait;
    private ReferralRepositoryInterface $referralRepository;
    private ReferralProgramService $referralProgramService;

    public function __construct(ReferralRepositoryInterface $referralRepository, referralProgramService $referralProgramService)
    {
        $this->referralRepository = $referralRepository;
        $this->referralProgramService = $referralProgramService;
    }

    public function execute(
        Array $referral_details,
    )
    {
        # Update attribute revenue by currency
        $referral_details_update_attribute_revenue = $this->referralProgramService->snUpdateAttributeRevenueByCurrency($referral_details);

        $new_data_referral = $this->referralRepository->createReferral($referral_details_update_attribute_revenue);

        return $new_data_referral;
    }
}
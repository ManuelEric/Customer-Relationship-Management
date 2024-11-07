<?php

namespace App\Actions\ReferralPrograms;

use App\Interfaces\ReferralRepositoryInterface;

class DeleteReferralProgramAction
{
    private ReferralRepositoryInterface $referralRepository;

    public function __construct(ReferralRepositoryInterface $referralRepository)
    {
        $this->referralRepository = $referralRepository;
    }

    public function execute(
        $referral_id,
    )
    {

        # deleted referral program
        $deleted_referral_program = $this->referralRepository->deleteReferral($referral_id);

        return $deleted_referral_program;
    }
}
<?php

namespace App\Services\Program;


class ReferralService 
{

    public function snSetRevenueByCurrency($referral_details)
    {
        if ($referral_details['currency'] != 'IDR') {
            $referral_details['revenue_other'] = $referral_details['revenue'];
            $referral_details['revenue'] = $referral_details['revenue_idr'];
            unset($referral_details['revenue_idr']);
        } else {
            unset($referral_details['revenue_idr']);
            unset($referral_details['curs_rate']);
        }

        return $referral_details;
    }

    public function snSetProgramByReferralType($referral_type)
    {
        if ($referral_type == "In")
            $newDetails['additional_prog_name'] = null;
        elseif ($referral_type == "Out")
            $newDetails['prog_id'] = null;
        return $newDetails;
    }
}
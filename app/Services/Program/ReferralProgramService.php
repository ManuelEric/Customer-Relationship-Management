<?php

namespace App\Services\Program;


class ReferralProgramService 
{

    # Purpose:
    # Update attribute revenue from referral details
    # Base on currency
    # IF currency 'IDR' THEN unset attribute revenue_idr & curs_rate
    # ELSE set attribute revenue_other to value revenue & set attribute revenue to value revenue_idr
    # END IF
    public function snUpdateAttributeRevenueByCurrency($referral_details)
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

    # Purpose:
    # IF referral_type "in" THEN update value additional_prog_name to NULL
    # ELSE update value prog_id to NULL
    # END IF
    public function snUpdateAttributeProgramByReferralType($referral_details)
    {
        if ($referral_details['referral_type'] == "In")
            $referral_details['additional_prog_name'] = null;
        elseif ($referral_details['referral_type'] == "Out")
            $referral_details['prog_id'] = null;
        return $referral_details;
    }
}
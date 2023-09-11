<?php

namespace App\Http\Traits;

trait CreateReferralCodeTrait
{

    public function createReferralCode($firstName, $clientId)
    {
        return strtoupper(substr($firstName,0,3)) . $clientId;
    }
}

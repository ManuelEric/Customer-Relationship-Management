<?php

namespace App\Actions\EdufLeads;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\EdufLeadRepositoryInterface;

class CreateEdufLeadAction
{
    use CreateCustomPrimaryKeyTrait, StandardizePhoneNumberTrait;
    private EdufLeadRepositoryInterface $edufLeadRepository;

    public function __construct(EdufLeadRepositoryInterface $edufLeadRepository)
    {
        $this->edufLeadRepository = $edufLeadRepository;
    }

    public function execute(
        Array $new_eduf_lead_details
    )
    {

        # store new eduf lead
        $ext_pic_phone = $this->tnSetPhoneNumber($new_eduf_lead_details['ext_pic_phone']);

        unset($new_eduf_lead_details['ext_pic_phone']); # remove the phone number that hasn't been updated into +62
        $new_eduf_lead_details['ext_pic_phone'] = $ext_pic_phone; # add new phone number 

        $new_eduf_lead = $this->edufLeadRepository->createEdufairLead($new_eduf_lead_details);

        return $new_eduf_lead;
    }
}
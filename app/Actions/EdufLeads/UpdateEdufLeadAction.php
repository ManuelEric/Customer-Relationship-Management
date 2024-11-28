<?php

namespace App\Actions\EdufLeads;

use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\EdufLeadRepositoryInterface;

class UpdateEdufLeadAction
{
    use StandardizePhoneNumberTrait;
    private EdufLeadRepositoryInterface $edufLeadRepository;

    public function __construct(EdufLeadRepositoryInterface $edufLeadRepository)
    {
        $this->edufLeadRepository = $edufLeadRepository;
    }

    public function execute(
        $eduf_lead_id,
        Array $new_eduf_lead_details
    )
    {
        $ext_pic_phone = $this->tnSetPhoneNumber($new_eduf_lead_details['ext_pic_phone']);
        
        unset($new_eduf_lead_details['ext_pic_phone']); # remove the phone number that hasn't been updated into +62
        $new_eduf_lead_details['ext_pic_phone'] = $ext_pic_phone; # add new phone number 

        if ($new_eduf_lead_details['organizer'] == "school"){
            $new_eduf_lead_details['corp_id'] = NULL;
        }else{
            $new_eduf_lead_details['sch_id'] = NULL;
        }

        unset($new_eduf_lead_details['organizer']);
        
        # Update eduf_lead
        $updated_eduf_lead = $this->edufLeadRepository->updateEdufairLead($eduf_lead_id, $new_eduf_lead_details);

        return $updated_eduf_lead;
    }
}
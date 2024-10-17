<?php

namespace App\Services\Program;

use App\Http\Requests\StoreClientEventRequest;
use App\Interfaces\ClientProgramLogMailRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ClientEventService 
{
    protected ClientRepositoryInterface $clientRepository;
    protected ClientProgramLogMailRepositoryInterface $clientProgramLogMailRepository;
    protected ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ClientProgramLogMailRepositoryInterface $clientProgramLogMailRepository, ClientRepositoryInterface $clientRepository, ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->clientProgramLogMailRepository = $clientProgramLogMailRepository;
        $this->clientRepository = $clientRepository;
        $this->clientProgramRepository = $clientProgramRepository;
    }


    public function snMappingLeads($leads, $type)
    {
        $leads = $leads->map(function ($item) use($type){
            return [
                'lead_id' => $item->lead_id,
                'conversion_lead' => $type == 'main_lead' ? $item->main_lead : $item->sub_lead
            ];
        });

        return $leads;
    }

    public function snSetRequestDataLead(StoreClientEventRequest $request, Array $new_client_event_details)
    {
        # set lead_id based on lead_id & kol_lead_id
        # when lead_id is kol
        # then put kol_lead_id to lead_id
        # otherwise
        # when lead_id is not kol
        # then lead_id is lead_id
        if ($request->lead_id == "kol") {

            unset($new_client_event_details['lead_id']);
            $new_client_event_details['eduf_id'] = null;
            $new_client_event_details['partner_id'] = null;
            $new_client_event_details['lead_id'] = $request->kol_lead_id;

            # LS010 = partner
        } else if ($request->lead_id == 'LS010') {
            $new_client_event_details['eduf_id'] = null;
        }
        # LS017 = external edufair
        else if ($request->lead_id != 'LS017' && $request->lead_id != 'kol') {

            $new_client_event_details['eduf_id'] = null;
            $new_client_event_details['partner_id'] = null;
        }
        # LS017 = external edufair
        else if ($request->lead_id != "kol" && $request->lead_id == 'LS017') {

            $new_client_event_details['partner_id'] = null;
        }

        return $new_client_event_details;
    }
}
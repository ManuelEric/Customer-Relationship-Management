<?php

namespace App\Services\Master;

use App\Interfaces\ClientRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LeadService 
{
    protected ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    # Purpose:
    # Set main_lead and sub lead before insert or update lead
    public function snSetMainLeadAndSubLead($request, $new_lead_details)
    {
        if ($request->kol == true) {

            $new_lead_details['main_lead'] = "KOL";
            $new_lead_details['sub_lead'] = $request->lead_name;
        } else {
            $new_lead_details['main_lead'] = $request->lead_name;
            $new_lead_details['sub_lead'] = null;
        }

        return $new_lead_details;
    }

    # purpose:
    # get list client
    # select secondary_id, first_name, last_name
    # use for select referral name
    public function snGetListReferral($request)
    {
        $grouped =  new Collection();

        if($request->ajax())
        {
            $filter['full_name'] = trim($request->term);
            $list_referral = $this->clientRepository->getListReferral(['secondary_id', 'first_name', 'last_name'], $filter);
    
            $grouped = $list_referral->mapToGroups(function ($item, $key) {
                return [
                    $item['data'] . 'results' => [
                        'id' => isset($item['secondary_id']) ? $item['secondary_id'] : null,
                        'text' => isset($item['first_name']) ? $item['first_name'] . ' ' . $item['last_name'] : null
                    ],
                ];
            });
    
            $more_pages=true;
            if (empty($list_referral->nextPageUrl())){
                $more_pages=false;
            }
    
            $grouped['pagination'] = [
                'more' => $more_pages
            ];
    
            return $grouped;
         
        }
    }
}
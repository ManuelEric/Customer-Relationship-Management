<?php

namespace App\Repositories;

use App\Interfaces\CorporatePartnerEventRepositoryInterface;
use App\Models\Event;
use Illuminate\Support\Carbon;

class CorporatePartnerEventRepository implements CorporatePartnerEventRepositoryInterface 
{
    public function getPartnerByEventId($eventId)
    {
        $event = Event::whereEventId($eventId);
        return $event->partner;
    }

    public function addPartnerEvent($eventId, $partnerDetails)
    {
        $event = Event::whereEventId($eventId);
        return $event->partner()->attach($partnerDetails, [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public function destroyPartnerEvent($eventId, $corporateId)
    {
        $event = Event::whereEventId($eventId);
        return $event->partner()->detach($corporateId);
    }
}
<?php

namespace App\Repositories;

use App\Interfaces\ClientEventLogMailRepositoryInterface;
use App\Models\Axis;
use App\Models\ClientEventLogMail;
use Illuminate\Support\Facades\DB;

class ClientEventLogMailRepository implements ClientEventLogMailRepositoryInterface
{
    public function getClientEventLogMail()
    {
        # find client event log mail that has sent_status = 0 and the event still up
        // return ClientEventLogMail::whereHas('clientEvent.event', function($query) {
        //         $query->where('event_enddate', '>', 'NOW()');
        //     })->
        //     where('sent_status', 0)->
        //     orderBy('created_at', 'asc')->get();
        return ClientEventLogMail::where('sent_status', 0)->
            orderBy('created_at', 'asc')->get();
    }

    public function createClientEventLogMail($logMailDetails)
    {
        return ClientEventLogMail::create($logMailDetails);
    }

    public function updateClientEventLogMail($id, $newDetails)
    {
        return ClientEventLogMail::find($id)->update($newDetails);
    }
}

<?php

namespace App\Repositories;

use App\Interfaces\ClientProgramLogMailRepositoryInterface;
use App\Models\Axis;
use App\Models\ClientEventLogMail;
use App\Models\ClientProgramLogMail;
use Illuminate\Support\Facades\DB;

class ClientProgramLogMailRepository implements ClientProgramLogMailRepositoryInterface
{
    public function getClientProgramLogMail()
    {
        # find client program log mail that has sent_status = 0
        return ClientProgramLogMail::
            whereHas('clientProgram.program', function($query) {
                $query->where('active', 1);
            })->
            where('sent_status', 0)->
            orderBy('created_at', 'asc')->get();
    }

    public function createClientProgramLogMail($logMailDetails)
    {
        return ClientProgramLogMail::create($logMailDetails);
    }

    public function updateClientProgramLogMail($id, $newDetails)
    {
        return ClientProgramLogMail::find($id)->update($newDetails);
    }
}

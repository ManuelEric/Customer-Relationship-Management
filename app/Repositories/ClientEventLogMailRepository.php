<?php

namespace App\Repositories;

use App\Interfaces\ClientEventLogMailRepositoryInterface;
use App\Models\Axis;
use App\Models\ClientEventLogMail;
use Illuminate\Support\Facades\DB;

class ClientEventLogMailRepository implements ClientEventLogMailRepositoryInterface
{
    public function createClientEventLogMail($logMailDetails)
    {
        return ClientEventLogMail::create($logMailDetails);
    }

    public function updateClientEventLogMail($id, $newDetails)
    {
        return ClientEventLogMail::find($id)->update($newDetails);
    }
}

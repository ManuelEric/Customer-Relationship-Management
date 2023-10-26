<?php

namespace App\Repositories;

use App\Interfaces\AcceptanceRepositoryInterface;
use App\Models\ClientAcceptance;
use App\Models\UserClient;
use Illuminate\Support\Facades\DB;

class AcceptanceRepository implements AcceptanceRepositoryInterface
{
    public function getAcceptanceById(int $id)
    {
        return DB::table('tbl_client_acceptance')->find($id);
    }

    public function getAcceptanceByClientId(int $clientId)
    {
        $client = UserClient::find($clientId);
        return $client->universityAcceptance;
    }

    public function deleteAcceptance(int $id)
    {
        $acceptance = DB::table('tbl_client_acceptance')->find($id);
        $clientId = $acceptance->client_id;
        $univId = $acceptance->univ_id;

        $client = UserClient::find($clientId);
        $client->universityAcceptance()->detach($univId);

        return $client;
    }

}

<?php

namespace App\Repositories;

use App\Interfaces\ClientRepositoryInterface;
use App\Models\UserClient;
use DataTables;

class ClientRepository implements ClientRepositoryInterface 
{
    public function getAllClientDataTables()
    {
        return Datatables::eloquent(UserClient::query())->make(true);
    }

    public function getAllClientByRole($roleName) # mentee, parent, teacher
    {

    }

    public function getClientById($clientId)
    {

    }

    public function deleteClient($clientId)
    {

    }

    public function createClient(array $clientDetails)
    {

    }

    public function updateClient($clientId, array $newDetails)
    {
        
    }
}
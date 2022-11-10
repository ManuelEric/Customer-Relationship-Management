<?php

namespace App\Interfaces;

interface ClientRepositoryInterface
{
    public function getAllClientDataTables();
    public function getAllClientByRole($roleName); # mentee, parent, teacher
    public function getClientById($clientId);
    public function deleteClient($clientId);
    public function createClient(array $clientDetails);
    public function updateClient($clientId, array $newDetails);
}
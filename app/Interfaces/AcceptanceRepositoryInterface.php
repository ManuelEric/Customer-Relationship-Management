<?php

namespace App\Interfaces;

interface AcceptanceRepositoryInterface 
{
    public function getAcceptanceById(int $id);
    public function getAcceptanceByClientId(int $clientId);
    public function deleteAcceptance(int $id);
}

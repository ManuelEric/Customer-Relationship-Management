<?php

namespace App\Interfaces;

interface AcceptanceRepositoryInterface 
{
    public function getAcceptanceById(int $id);
    public function getAcceptanceByClientId(String $clientId);
    public function deleteAcceptance(int $id);
}

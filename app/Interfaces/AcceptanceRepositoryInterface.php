<?php

namespace App\Interfaces;

interface AcceptanceRepositoryInterface
{
    public function getAcceptanceById(int $id);

    public function getAcceptanceByClientId(string $clientId);

    public function deleteAcceptance(int $id);
}

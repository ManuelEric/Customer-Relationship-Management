<?php

namespace App\Actions\ClientEvents;

use App\Interfaces\ClientEventRepositoryInterface;

class DeleteClientEventAction
{
    private ClientEventRepositoryInterface $clientEventRepository;

    public function __construct(ClientEventRepositoryInterface $clientEventRepository)
    {
        $this->clientEventRepository = $clientEventRepository;
    }

    public function execute(
        $clientevent_id
    )
    {
        # delete client event
        $deleted_client_event = $this->clientEventRepository->deleteClientEvent($clientevent_id);

        return $deleted_client_event;
    }
}
<?php

namespace App\Actions\ClientEvents;

use App\Http\Traits\GenerateTicketIdTrait;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Services\Program\ClientEventService;

class UpdateClientEventAction
{
    use GenerateTicketIdTrait;
    private ClientEventRepositoryInterface $clientEventRepository;
    private ClientEventService $clientEventService;

    public function __construct(ClientEventRepositoryInterface $clientEventRepository, ClientEventService $clientEventService)
    {
        $this->clientEventRepository = $clientEventRepository;
        $this->clientEventService = $clientEventService;
    }

    public function execute(
        $clientevent_id,
        array $new_client_event_details
    ) {

        $new_client_event = $this->clientEventRepository->updateClientEvent($clientevent_id, $new_client_event_details);

        # Generate ticket ID when the event is offline or hybrid
        # Updated generate ticket ID for all events 

        // if (in_array($new_client_event->event->type, ['offline', 'hybrid'])) {

        // $ticketID = app('App\Http\Controllers\Api\v1\ExtClientController')->generateTicketID();
        $ticket_id = $this->tnGenerateTicketId();
        $this->clientEventRepository->updateClientEvent($new_client_event->clientevent_id, ['ticket_id' => $ticket_id]);
        // }
        
        //! it supposed to be a function to remove the ticket ID when the event was changed into online (yet to be developed)


        return $new_client_event;
    }
}

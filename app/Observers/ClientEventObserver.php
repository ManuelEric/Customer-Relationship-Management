<?php

namespace App\Observers;

use App\Interfaces\ClientEventLogMailRepositoryInterface;
use App\Jobs\Event\General\ProcessEmailConfirmation;
use App\Models\ClientEvent;

class ClientEventObserver
{
    protected ClientEventLogMailRepositoryInterface $clientEventLogMailRepository;
    
    public function __construct(ClientEventLogMailRepositoryInterface $clientEventLogMailRepository)
    {
        $this->clientEventLogMailRepository = $clientEventLogMailRepository;
    }
    
    public function created(ClientEvent $client_event)
    {
        ProcessEmailConfirmation::dispatch($client_event, $this->clientEventLogMailRepository)->onQueue('email-confirmation-event');
    }
}

<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\ClientEvent;

class ClientEventObserver
{
     /**
     * Handle the ClientEvent "created" event.
     */
    public function created(ClientEvent $clientEvent): void
    {

        // Send to pusher
        event(New MessageSent('rt_client_event', 'channel_datatable'));
    }

    /**
     * Handle the ClientEvent "updated" event.
     */
    public function updated(ClientEvent $clientEvent): void
    {
        // Send to pusher
        event(New MessageSent('rt_client_event', 'channel_datatable'));
    }

    /**
     * Handle the ClientEvent "deleted" event.
     */
    public function deleted(ClientEvent $clientEvent): void
    {
        // Send to pusher
        event(New MessageSent('rt_client_event', 'channel_datatable'));
    }

    /**
     * Handle the ClientEvent "restored" event.
     */
    public function restored(ClientEvent $clientEvent): void
    {
        //
    }

    /**
     * Handle the ClientEvent "force deleted" event.
     */
    public function forceDeleted(ClientEvent $clientEvent): void
    {
        //
    }
}

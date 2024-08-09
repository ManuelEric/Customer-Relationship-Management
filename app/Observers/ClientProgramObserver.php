<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\ClientProgram;

class ClientProgramObserver
{
     /**
     * Handle the ClientProgram "created" event.
     */
    public function created(ClientProgram $clientProgram): void
    {

        // Send to pusher
        event(New MessageSent('rt_client_program', 'channel_datatable'));
    }

    /**
     * Handle the ClientProgram "updated" event.
     */
    public function updated(ClientProgram $clientProgram): void
    {
        // Send to pusher
        event(New MessageSent('rt_client', 'channel_datatable'));
    }

    /**
     * Handle the ClientProgram "deleted" event.
     */
    public function deleted(ClientProgram $clientProgram): void
    {
        // Send to pusher
        event(New MessageSent('rt_client', 'channel_datatable'));
    }

    /**
     * Handle the ClientProgram "restored" event.
     */
    public function restored(ClientProgram $clientProgram): void
    {
        //
    }

    /**
     * Handle the ClientProgram "force deleted" event.
     */
    public function forceDeleted(ClientProgram $clientProgram): void
    {
        //
    }
}

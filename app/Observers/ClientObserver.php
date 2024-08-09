<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Events\UpdateDatatableEvent;
use App\Models\UserClient;
use Illuminate\Support\Facades\Log;

class ClientObserver
{
    /**
     * Handle the UserClient "created" event.
     */
    public function created(UserClient $userClient): void
    {

        // Send to pusher
        event(New MessageSent('rt_client', 'channel_datatable'));
    }

    /**
     * Handle the UserClient "updated" event.
     */
    public function updated(UserClient $userClient): void
    {
        // Send to pusher
        event(New MessageSent('rt_client', 'channel_datatable'));
    }

    /**
     * Handle the UserClient "deleted" event.
     */
    public function deleted(UserClient $userClient): void
    {
        // Send to pusher
        event(New MessageSent('rt_client', 'channel_datatable'));
    }

    /**
     * Handle the UserClient "restored" event.
     */
    public function restored(UserClient $userClient): void
    {
        //
    }

    /**
     * Handle the UserClient "force deleted" event.
     */
    public function forceDeleted(UserClient $userClient): void
    {
        //
    }
}

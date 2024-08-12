<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {

        // Send to pusher
        event(New MessageSent('rt_user', 'channel_datatable'));
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Send to pusher
        event(New MessageSent('rt_user', 'channel_datatable'));
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Send to pusher
        event(New MessageSent('rt_user', 'channel_datatable'));
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}

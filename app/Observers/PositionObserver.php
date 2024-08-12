<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\Position;

class PositionObserver
{
    /**
     * Handle the Position "created" event.
     */
    public function created(Position $position): void
    {

        // Send to pusher
        event(New MessageSent('rt_position', 'channel_datatable'));
    }

    /**
     * Handle the Position "updated" event.
     */
    public function updated(Position $position): void
    {
        // Send to pusher
        event(New MessageSent('rt_position', 'channel_datatable'));
    }

    /**
     * Handle the Position "deleted" event.
     */
    public function deleted(Position $position): void
    {
        // Send to pusher
        event(New MessageSent('rt_position', 'channel_datatable'));
    }

    /**
     * Handle the Position "restored" event.
     */
    public function restored(Position $position): void
    {
        //
    }

    /**
     * Handle the Position "force deleted" event.
     */
    public function forceDeleted(Position $position): void
    {
        //
    }
}

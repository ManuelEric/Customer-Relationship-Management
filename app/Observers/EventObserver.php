<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\Event;

class EventObserver
{
     /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {

        // Send to pusher
        event(New MessageSent('rt_event', 'channel_datatable'));
    }

    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        // Send to pusher
        event(New MessageSent('rt_event', 'channel_datatable'));
    }

    /**
     * Handle the Event "deleted" event.
     */
    public function deleted(Event $event): void
    {
        // Send to pusher
        event(New MessageSent('rt_event', 'channel_datatable'));
    }

    /**
     * Handle the Event "restored" event.
     */
    public function restored(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "force deleted" event.
     */
    public function forceDeleted(Event $event): void
    {
        //
    }
}

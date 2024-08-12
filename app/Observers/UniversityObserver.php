<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\University;

class UniversityObserver
{
     /**
     * Handle the University "created" event.
     */
    public function created(University $university): void
    {

        // Send to pusher
        event(New MessageSent('rt_university', 'channel_datatable'));
    }

    /**
     * Handle the University "updated" event.
     */
    public function updated(University $university): void
    {
        // Send to pusher
        event(New MessageSent('rt_university', 'channel_datatable'));
    }

    /**
     * Handle the University "deleted" event.
     */
    public function deleted(University $university): void
    {
        // Send to pusher
        event(New MessageSent('rt_university', 'channel_datatable'));
    }

    /**
     * Handle the University "restored" event.
     */
    public function restored(University $university): void
    {
        //
    }

    /**
     * Handle the University "force deleted" event.
     */
    public function forceDeleted(University $university): void
    {
        //
    }
}

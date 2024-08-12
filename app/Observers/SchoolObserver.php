<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\School;

class SchoolObserver
{
    /**
     * Handle the School "created" event.
     */
    public function created(School $school): void
    {

        // Send to pusher
        event(New MessageSent('rt_school', 'channel_datatable'));
    }

    /**
     * Handle the School "updated" event.
     */
    public function updated(School $school): void
    {
        // Send to pusher
        event(New MessageSent('rt_school', 'channel_datatable'));
    }

    /**
     * Handle the School "deleted" event.
     */
    public function deleted(School $school): void
    {
        // Send to pusher
        event(New MessageSent('rt_school', 'channel_datatable'));
    }

    /**
     * Handle the School "restored" event.
     */
    public function restored(School $school): void
    {
        //
    }

    /**
     * Handle the School "force deleted" event.
     */
    public function forceDeleted(School $school): void
    {
        //
    }
}

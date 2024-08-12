<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\Program;

class ProgramObserver
{
     /**
     * Handle the Program "created" event.
     */
    public function created(Program $program): void
    {

        // Send to pusher
        event(New MessageSent('rt_program', 'channel_datatable'));
    }

    /**
     * Handle the Program "updated" event.
     */
    public function updated(Program $program): void
    {
        // Send to pusher
        event(New MessageSent('rt_program', 'channel_datatable'));
    }

    /**
     * Handle the Program "deleted" event.
     */
    public function deleted(Program $program): void
    {
        // Send to pusher
        event(New MessageSent('rt_program', 'channel_datatable'));
    }

    /**
     * Handle the Program "restored" event.
     */
    public function restored(Program $program): void
    {
        //
    }

    /**
     * Handle the Program "force deleted" event.
     */
    public function forceDeleted(Program $program): void
    {
        //
    }
}

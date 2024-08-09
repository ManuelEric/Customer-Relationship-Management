<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\SchoolProgram;

class SchoolProgramObserver
{
     /**
     * Handle the SchoolProgram "created" event.
     */
    public function created(SchoolProgram $schoolProgram): void
    {

        // Send to pusher
        event(New MessageSent('rt_school_program', 'channel_datatable'));
    }

    /**
     * Handle the SchoolProgram "updated" event.
     */
    public function updated(SchoolProgram $schoolProgram): void
    {
        // Send to pusher
        event(New MessageSent('rt_school_program', 'channel_datatable'));
    }

    /**
     * Handle the SchoolProgram "deleted" event.
     */
    public function deleted(SchoolProgram $schoolProgram): void
    {
        // Send to pusher
        event(New MessageSent('rt_school_program', 'channel_datatable'));
    }

    /**
     * Handle the SchoolProgram "restored" event.
     */
    public function restored(SchoolProgram $schoolProgram): void
    {
        //
    }

    /**
     * Handle the SchoolProgram "force deleted" event.
     */
    public function forceDeleted(SchoolProgram $schoolProgram): void
    {
        //
    }
}

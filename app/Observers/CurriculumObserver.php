<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\Curriculum;

class CurriculumObserver
{
    /**
     * Handle the Curriculum "created" event.
     */
    public function created(Curriculum $curriculum): void
    {

        // Send to pusher
        event(New MessageSent('rt_curriculum', 'channel_datatable'));
    }

    /**
     * Handle the Curriculum "updated" event.
     */
    public function updated(Curriculum $curriculum): void
    {
        // Send to pusher
        event(New MessageSent('rt_curriculum', 'channel_datatable'));
    }

    /**
     * Handle the Curriculum "deleted" event.
     */
    public function deleted(Curriculum $curriculum): void
    {
        // Send to pusher
        event(New MessageSent('rt_curriculum', 'channel_datatable'));
    }

    /**
     * Handle the Curriculum "restored" event.
     */
    public function restored(Curriculum $curriculum): void
    {
        //
    }

    /**
     * Handle the Curriculum "force deleted" event.
     */
    public function forceDeleted(Curriculum $curriculum): void
    {
        //
    }
}

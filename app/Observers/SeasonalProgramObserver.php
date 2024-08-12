<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\SeasonalProgram;

class SeasonalProgramObserver
{
     /**
     * Handle the SeasonalProgram "created" event.
     */
    public function created(SeasonalProgram $seasonalProgram): void
    {

        // Send to pusher
        event(New MessageSent('rt_seasonal_program', 'channel_datatable'));
    }

    /**
     * Handle the SeasonalProgram "updated" event.
     */
    public function updated(SeasonalProgram $seasonalProgram): void
    {
        // Send to pusher
        event(New MessageSent('rt_seasonal_program', 'channel_datatable'));
    }

    /**
     * Handle the SeasonalProgram "deleted" event.
     */
    public function deleted(SeasonalProgram $seasonalProgram): void
    {
        // Send to pusher
        event(New MessageSent('rt_seasonal_program', 'channel_datatable'));
    }

    /**
     * Handle the SeasonalProgram "restored" event.
     */
    public function restored(SeasonalProgram $seasonalProgram): void
    {
        //
    }

    /**
     * Handle the SeasonalProgram "force deleted" event.
     */
    public function forceDeleted(SeasonalProgram $seasonalProgram): void
    {
        //
    }
}

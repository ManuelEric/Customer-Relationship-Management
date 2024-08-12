<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\Corporate;

class PartnerObserver
{
    /**
     * Handle the Corporate "created" event.
     */
    public function created(Corporate $partner): void
    {

        // Send to pusher
        event(New MessageSent('rt_partner', 'channel_datatable'));
    }

    /**
     * Handle the Corporate "updated" event.
     */
    public function updated(Corporate $partner): void
    {
        // Send to pusher
        event(New MessageSent('rt_partner', 'channel_datatable'));
    }

    /**
     * Handle the Corporate "deleted" event.
     */
    public function deleted(Corporate $partner): void
    {
        // Send to pusher
        event(New MessageSent('rt_partner', 'channel_datatable'));
    }

    /**
     * Handle the Corporate "restored" event.
     */
    public function restored(Corporate $partner): void
    {
        //
    }

    /**
     * Handle the Corporate "force deleted" event.
     */
    public function forceDeleted(Corporate $partner): void
    {
        //
    }
}

<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\Lead;

class LeadObserver
{
     /**
     * Handle the UserClient "created" event.
     */
    public function created(Lead $lead): void
    {

        // Send to pusher
        event(New MessageSent('rt_lead', 'channel_datatable'));
    }

    /**
     * Handle the Lead "updated" event.
     */
    public function updated(Lead $lead): void
    {
        // Send to pusher
        event(New MessageSent('rt_lead', 'channel_datatable'));
    }

    /**
     * Handle the Lead "deleted" event.
     */
    public function deleted(Lead $lead): void
    {
        // Send to pusher
        event(New MessageSent('rt_lead', 'channel_datatable'));
    }

    /**
     * Handle the Lead "restored" event.
     */
    public function restored(Lead $lead): void
    {
        //
    }

    /**
     * Handle the Lead "force deleted" event.
     */
    public function forceDeleted(Lead $lead): void
    {
        //
    }
}

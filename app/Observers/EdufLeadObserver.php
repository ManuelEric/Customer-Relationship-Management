<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\EdufLead;

class EdufLeadObserver
{
     /**
     * Handle the EdufLead "created" event.
     */
    public function created(EdufLead $edufLead): void
    {

        // Send to pusher
        event(New MessageSent('rt_eduf_lead', 'channel_datatable'));
    }

    /**
     * Handle the EdufLead "updated" event.
     */
    public function updated(EdufLead $edufLead): void
    {
        // Send to pusher
        event(New MessageSent('rt_eduf_lead', 'channel_datatable'));
    }

    /**
     * Handle the EdufLead "deleted" event.
     */
    public function deleted(EdufLead $edufLead): void
    {
        // Send to pusher
        event(New MessageSent('rt_eduf_lead', 'channel_datatable'));
    }

    /**
     * Handle the EdufLead "restored" event.
     */
    public function restored(EdufLead $edufLead): void
    {
        //
    }

    /**
     * Handle the EdufLead "force deleted" event.
     */
    public function forceDeleted(EdufLead $edufLead): void
    {
        //
    }
}

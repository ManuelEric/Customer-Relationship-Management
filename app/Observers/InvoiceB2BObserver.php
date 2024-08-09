<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\Invb2b;

class InvoiceB2BObserver
{
     /**
     * Handle the Invb2b "created" event.
     */
    public function created(Invb2b $invB2b): void
    {

        // Send to pusher
        event(New MessageSent('rt_invoice_b2b', 'channel_datatable'));
    }

    /**
     * Handle the Invb2b "updated" event.
     */
    public function updated(Invb2b $invB2b): void
    {
        // Send to pusher
        event(New MessageSent('rt_invoice_b2b', 'channel_datatable'));
    }

    /**
     * Handle the Invb2b "deleted" event.
     */
    public function deleted(Invb2b $invB2b): void
    {
        // Send to pusher
        event(New MessageSent('rt_invoice_b2b', 'channel_datatable'));
    }

    /**
     * Handle the Invb2b "restored" event.
     */
    public function restored(Invb2b $invB2b): void
    {
        //
    }

    /**
     * Handle the Invb2b "force deleted" event.
     */
    public function forceDeleted(Invb2b $invB2b): void
    {
        //
    }
}

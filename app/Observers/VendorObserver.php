<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\Vendor;

class VendorObserver
{
     /**
     * Handle the Vendor "created" event.
     */
    public function created(Vendor $vendor): void
    {

        // Send to pusher
        event(New MessageSent('rt_vendor', 'channel_datatable'));
    }

    /**
     * Handle the Vendor "updated" event.
     */
    public function updated(Vendor $vendor): void
    {
        // Send to pusher
        event(New MessageSent('rt_vendor', 'channel_datatable'));
    }

    /**
     * Handle the Vendor "deleted" event.
     */
    public function deleted(Vendor $vendor): void
    {
        // Send to pusher
        event(New MessageSent('rt_vendor', 'channel_datatable'));
    }

    /**
     * Handle the Vendor "restored" event.
     */
    public function restored(Vendor $vendor): void
    {
        //
    }

    /**
     * Handle the Vendor "force deleted" event.
     */
    public function forceDeleted(Vendor $vendor): void
    {
        //
    }
}

<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\SalesTarget;

class SalesTargetObserver
{
     /**
     * Handle the SalesTarget "created" event.
     */
    public function created(SalesTarget $salesTarget): void
    {

        // Send to pusher
        event(New MessageSent('rt_sales_target', 'channel_datatable'));
    }

    /**
     * Handle the SalesTarget "updated" event.
     */
    public function updated(SalesTarget $salesTarget): void
    {
        // Send to pusher
        event(New MessageSent('rt_sales_target', 'channel_datatable'));
    }

    /**
     * Handle the SalesTarget "deleted" event.
     */
    public function deleted(SalesTarget $salesTarget): void
    {
        // Send to pusher
        event(New MessageSent('rt_sales_target', 'channel_datatable'));
    }

    /**
     * Handle the SalesTarget "restored" event.
     */
    public function restored(SalesTarget $salesTarget): void
    {
        //
    }

    /**
     * Handle the UserClient "force deleted" event.
     */
    public function forceDeleted(SalesTarget $salesTarget): void
    {
        //
    }
}

<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\Receipt;

class ReceiptObserver
{
     /**
     * Handle the Receipt "created" event.
     */
    public function created(Receipt $receipt): void
    {

        // Send to pusher
        event(New MessageSent('rt_receipt', 'channel_datatable'));
    }

    /**
     * Handle the Receipt "updated" event.
     */
    public function updated(Receipt $receipt): void
    {
        // Send to pusher
        event(New MessageSent('rt_receipt', 'channel_datatable'));
    }

    /**
     * Handle the Receipt "deleted" event.
     */
    public function deleted(Receipt $receipt): void
    {
        // Send to pusher
        event(New MessageSent('rt_receipt', 'channel_datatable'));
    }

    /**
     * Handle the Receipt "restored" event.
     */
    public function restored(Receipt $receipt): void
    {
        //
    }

    /**
     * Handle the Receipt "force deleted" event.
     */
    public function forceDeleted(Receipt $receipt): void
    {
        //
    }
}

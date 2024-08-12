<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\PurchaseRequest;

class PurchaseRequestObserver
{
     /**
     * Handle the PurchaseRequest "created" event.
     */
    public function created(PurchaseRequest $purchaseRequest): void
    {

        // Send to pusher
        event(New MessageSent('rt_purchase_request', 'channel_datatable'));
    }

    /**
     * Handle the PurchaseRequest "updated" event.
     */
    public function updated(PurchaseRequest $purchaseRequest): void
    {
        // Send to pusher
        event(New MessageSent('rt_purchase_request', 'channel_datatable'));
    }

    /**
     * Handle the PurchaseRequest "deleted" event.
     */
    public function deleted(PurchaseRequest $purchaseRequest): void
    {
        // Send to pusher
        event(New MessageSent('rt_purchase_request', 'channel_datatable'));
    }

    /**
     * Handle the PurchaseRequest "restored" event.
     */
    public function restored(PurchaseRequest $purchaseRequest): void
    {
        //
    }

    /**
     * Handle the PurchaseRequest "force deleted" event.
     */
    public function forceDeleted(PurchaseRequest $purchaseRequest): void
    {
        //
    }
}

<?php

namespace App\Observers;

use App\Events\MessageSent;
use App\Models\InvoiceProgram;

class InvoiceB2CObserver
{
     /**
     * Handle the InvoiceProgram "created" event.
     */
    public function created(InvoiceProgram $invoiceProgram): void
    {

        // Send to pusher
        event(New MessageSent('rt_invoice_b2c', 'channel_datatable'));
    }

    /**
     * Handle the InvoiceProgram "updated" event.
     */
    public function updated(InvoiceProgram $invoiceProgram): void
    {
        // Send to pusher
        event(New MessageSent('rt_invoice_b2c', 'channel_datatable'));
    }

    /**
     * Handle the InvoiceProgram "deleted" event.
     */
    public function deleted(InvoiceProgram $invoiceProgram): void
    {
        // Send to pusher
        event(New MessageSent('rt_invoice_b2c', 'channel_datatable'));
    }

    /**
     * Handle the InvoiceProgram "restored" event.
     */
    public function restored(InvoiceProgram $invoiceProgram): void
    {
        //
    }

    /**
     * Handle the InvoiceProgram "force deleted" event.
     */
    public function forceDeleted(InvoiceProgram $invoiceProgram): void
    {
        //
    }
}

<?php

namespace App\Listeners;

use App\Models\Invb2b;
use App\Models\InvDetail;
use App\Models\InvoiceProgram;
use App\Models\v1\InvoiceDetail;
use App\Models\v1\InvoiceDtl;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ReminderInvoiceToClientListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageSent $event): void
    {
        /* when the title was not setup by the mail provider */
        if ( !array_key_exists('__laravel_mailable', $event->data))
            return;

        switch ($event->data['__laravel_mailable'])
        {
            case "App\\Mail\\Invoice\\ReminderToClient":
                # after mail sent to client
                # system will update column `reminded` on tbl_inv or tbl_inv_dtl and turn it into 1
                # and since invoice has 2 type, which is invoice full payment and invoice installment
                # we need to know, whether it is full payment or installment
                $invoice_id = $event->data['content']['invoice_id'];
                $invoice_payment_method = $event->data['content']['inv_paymentmethod'];
                if ( preg_match('/installment/i', $invoice_payment_method) ) // if payment method is contains "installment"
                {
                    InvDetail::where('inv_id', $invoice_id)->where('invdtl_installment', $invoice_payment_method)->update(['reminded' => 1]);
                    Log::debug('[MESSAGE SENT]', [
                        'invoice_id' => $invoice_id,
                        'invoice_payment_method' => $invoice_payment_method
                    ]);
                }
                else // if payment method is full payment
                {
                    InvoiceProgram::where('inv_id', $invoice_id)->update(['reminded' => 1]);
                    Log::debug('[MESSAGE SENT]', [
                        'invoice_id' => $invoice_id,
                        'invoice_payment_method' => $invoice_payment_method
                    ]);
                }
                break;

            case "App\\Mail\\Invoice\\ReminderToPartner":
                # after mail sent to partner
                # system will update column `reminded` on tbl_invb2b and turn it into 1
                $invoiceb2b_id = $event->data['content']['invoiceb2b_id'];
                Invb2b::where('invb2b_id', $invoiceb2b_id)->update(['reminded' => 1]);
                break;
        }
    }
}

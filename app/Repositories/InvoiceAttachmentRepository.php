<?php

namespace App\Repositories;

use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use App\Models\InvoiceAttachment;

class InvoiceAttachmentRepository implements InvoiceAttachmentRepositoryInterface
{
    public function getInvoiceAttachmentById($invAttachment_id)
    {
        return InvoiceAttachment::find($invAttachment_id);
    }

    public function getInvoiceAttachmentByInvoiceCurrency($invoice_type, $identifier, $currency)
    {
        $available_currency = ['idr', 'usd'];
        $existed_currency = [];
        foreach ($available_currency as $index => $value) {
            if (InvoiceAttachment::selectAttachment($invoice_type, $identifier, $value)->exists()) {
                $existed_currency[$index] = $value;
            }
        }

        // there's a condition where currency that controller carried was different with the data inside inv_attachment table
        // in order to make this features keep working
        // we allow system to check which currency that exists
        if (! in_array($currency, $existed_currency)) {
            $currency = $existed_currency;
        }

        return InvoiceAttachment::selectAttachment($invoice_type, $identifier, $currency)->first();
    }

    public function getInvoiceAttachmentByInvoiceIdentifier($invoiceType, $identifier)
    {
        return InvoiceAttachment::when($invoiceType == 'Program', function ($query) use ($identifier) {
            $query->where('inv_id', $identifier);
        })->when($invoiceType == 'B2B', function ($query) use ($identifier) {
            $query->where('invb2b_id', $identifier);
        })->get();
    }

    public function createInvoiceAttachment(array $invoiceAttachments)
    {
        return InvoiceAttachment::create($invoiceAttachments);
    }

    public function updateInvoiceAttachment($invAttachment_id, array $newDetails)
    {
        return InvoiceAttachment::whereId($invAttachment_id)
            ->update($newDetails);
    }

    public function deleteInvoiceAttachment($invAttachment_id)
    {
        return InvoiceAttachment::whereId($invAttachment_id)->delete();
    }

    public function deleteInvoiceAttachmentByInvoiceId($invoiceId)
    {
        return InvoiceAttachment::where('inv_id', $invoiceId)->delete();
    }

    public function deleteInvoiceAttachmentByInvoiceB2bId($invb2b_id)
    {
        return InvoiceAttachment::where('invb2b_id', $invb2b_id)->delete();
    }
}

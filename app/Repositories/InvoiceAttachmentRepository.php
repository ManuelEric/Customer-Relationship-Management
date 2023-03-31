<?php

namespace App\Repositories;

use App\Interfaces\InvoiceAttachmentRepositoryInterface;
use App\Models\Invb2b;
use App\Models\InvoiceAttachment;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\DB;

class InvoiceAttachmentRepository implements InvoiceAttachmentRepositoryInterface
{
    public function getInvoiceAttachmentById($invAttachment_id)
    {
        return InvoiceAttachment::find($invAttachment_id);
    }

    public function getInvoiceAttachmentByInvoiceCurrency($invoiceType, $identifier, $currency)
    {
        return InvoiceAttachment::when($invoiceType == "Program", function ($query) use ($identifier, $currency) {
            $query->where('inv_id', $identifier)->where('currency', $currency);
        })->when($invoiceType == "B2B", function ($query) use ($identifier, $currency) {
            $query->where('invb2b_id', $identifier)->where('currency', $currency);
        })->first();
    }

    public function getInvoiceAttachmentByInvoiceIdentifier($invoiceType, $identifier)
    {
        return InvoiceAttachment::when($invoiceType == "Program", function ($query) use ($identifier) {
            $query->where('inv_id', $identifier);
        })->when($invoiceType == "B2B", function ($query) use ($identifier) {
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

    public function deleteInvoiceAttachmentByInvoiceB2bId($invb2b_id)
    {
        return InvoiceAttachment::where('invb2b_id', $invb2b_id)->delete();
    }
}

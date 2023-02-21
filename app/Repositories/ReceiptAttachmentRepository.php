<?php

namespace App\Repositories;

use App\Interfaces\ReceiptAttachmentRepositoryInterface;
use App\Models\Invb2b;
use App\Models\ReceiptAttachment;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\DB;

class ReceiptAttachmentRepository implements ReceiptAttachmentRepositoryInterface
{
    public function getReceiptAttachmentById($recAttachment_id)
    {
        return ReceiptAttachment::find($recAttachment_id);
    }

    public function getReceiptAttachmentByReceiptId($receipt_id, $currency)
    {
        return ReceiptAttachment::where('receipt_id', $receipt_id)->where('currency', $currency)->first();
    }

    public function getReceiptAttachmentByInvoiceCurrency($invoiceType, $identifier, $currency)
    {
        return ReceiptAttachment::when($invoiceType == "Program", function ($query) use ($identifier, $currency) {
            $query->where('inv_id', $identifier)->where('currency', $currency);
        })->when($invoiceType == "B2B", function ($query) use ($identifier, $currency) {
            $query->where('invb2b_id', $identifier)->where('currency', $currency);
        })->when($invoiceType == "installment", function ($query) use ($identifier, $currency) {
            $query->where('invdtl_id', $identifier)->where('currency', $currency);
        })->first();
    }

    public function getReceiptAttachmentByInvoiceIdentifier($invoiceType, $identifier)
    {
        return ReceiptAttachment::when($invoiceType == "Program", function ($query) use ($identifier) {
            $query->where('inv_id', $identifier);
        })->when($invoiceType == "B2B", function ($query) use ($identifier) {
            $query->where('invb2b_id', $identifier);
        })->when($invoiceType == "installment", function ($query) use ($identifier) {
            $query->where('invdtl_id', $identifier);
        })->get();
    }

    public function createReceiptAttachment(array $receiptAttachments)
    {
        return ReceiptAttachment::updateOrCreate($receiptAttachments);
    }

    public function updateReceiptAttachment($recAttachment_id, array $newDetails)
    {
        return ReceiptAttachment::whereId($recAttachment_id)
            ->update($newDetails);
    }

    public function deleteReceiptAttachment($recAttachment_id)
    {
        return ReceiptAttachment::whereId($recAttachment_id)->delete();
    }
}

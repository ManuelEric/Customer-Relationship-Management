<?php

namespace App\Repositories;

use App\Interfaces\ReceiptRepositoryInterface;
use App\Models\Receipt;

class ReceiptRepository implements ReceiptRepositoryInterface
{
    public function getReceiptByInvoiceIdentifier($invoiceType, $identifier)
    {
        return Receipt::when($invoiceType == "Program", function($query) use ($identifier) {
            $query->where('inv_id', $identifier);
        })->when($invoiceType == "Installment", function($query) use ($identifier) {
            $query->where('invdtl_id', $identifier);
        })->when($invoiceType == "B2B", function ($query) use ($identifier) {
            $query->where('invb2b_id', $identifier);
        })->first();
    }

    public function createReceipt(array $receiptDetails)
    {
        return Receipt::create($receiptDetails);
    }

    public function updateReceipt($receiptId, array $newDetails)
    {
        return Receipt::whereId($receiptId)->update($newDetails);
    }

    public function deleteReceipt($receiptId)
    {
        return Receipt::whereId($receiptId)->delete();
    }
}

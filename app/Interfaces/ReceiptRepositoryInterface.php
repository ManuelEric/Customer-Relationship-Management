<?php

namespace App\Interfaces;

interface ReceiptRepositoryInterface
{
    public function getAllReceiptSchDataTables();
    public function getReceiptById($receiptId);
    public function getReceiptByInvoiceIdentifier($invoiceType, $identifier);
    public function createReceipt(array $receiptDetails);
    public function updateReceipt($receiptId, array $newDetails);
    public function deleteReceipt($receiptId);
}

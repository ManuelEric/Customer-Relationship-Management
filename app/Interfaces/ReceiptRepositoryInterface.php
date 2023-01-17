<?php

namespace App\Interfaces;

interface ReceiptRepositoryInterface
{
    public function getAllReceiptSchDataTables();
    public function getAllReceiptByStatusDataTables($status);
    public function getReceiptByInvoiceIdentifier($invoiceType, $identifier);
    public function getReceiptById($receiptId);
    public function createReceipt(array $receiptDetails);
    public function updateReceipt($receiptId, array $newDetails);
    public function deleteReceipt($receiptId);
}

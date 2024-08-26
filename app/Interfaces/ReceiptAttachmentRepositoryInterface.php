<?php

namespace App\Interfaces;

interface ReceiptAttachmentRepositoryInterface
{
    public function getReceiptAttachmentById($recAttachment_id);
    public function getReceiptAttachmentByReceiptId($receipt_id, $currency);
    public function getReceiptAttachmentByInvoiceCurrency($invoiceType, $identifier, $currency);
    public function getReceiptAttachmentByInvoiceIdentifier($invoiceType, $identifier);
    public function createReceiptAttachment(array $receuotAttachments);
    public function updateReceiptAttachment($recAttachment_id, array $newDetails);
    public function deleteReceiptAttachment($recAttachment_id);
}

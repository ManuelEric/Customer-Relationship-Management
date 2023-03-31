<?php

namespace App\Interfaces;

interface InvoiceAttachmentRepositoryInterface
{
    public function getInvoiceAttachmentById($invAttachment_id);
    public function getInvoiceAttachmentByInvoiceCurrency($invoiceType, $identifier, $currency);
    public function getInvoiceAttachmentByInvoiceIdentifier($invoiceType, $identifier);
    public function createInvoiceAttachment(array $invoiceAttachments);
    public function updateInvoiceAttachment($invAttachment_id, array $newDetails);
    public function deleteInvoiceAttachment($invAttachment_id);
    public function deleteInvoiceAttachmentByInvoiceB2bId($invb2b_id);
}

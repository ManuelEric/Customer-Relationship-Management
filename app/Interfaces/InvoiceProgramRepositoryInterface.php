<?php

namespace App\Interfaces;

interface InvoiceProgramRepositoryInterface 
{
    public function getAllInvoiceProgramDataTables($status);
    public function getInvoiceByClientProgId($clientProgId);
    public function createInvoice(array $invoiceDetails);
    public function updateInvoice($invoiceId, array $invoiceDetails);
    public function deleteInvoiceByClientProgId($clientProgId);
}
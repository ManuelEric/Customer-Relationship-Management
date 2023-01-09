<?php

namespace App\Interfaces;

interface InvoiceProgramRepositoryInterface 
{
    public function getAllInvoiceProgramDataTables($status);
    public function createInvoice(array $invoiceDetails);
}
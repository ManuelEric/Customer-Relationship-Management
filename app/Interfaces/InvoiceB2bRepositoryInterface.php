<?php

namespace App\Interfaces;

interface InvoiceB2bRepositoryInterface
{
    public function getAllInvoiceNeededSchDataTables();
    public function getAllInvoiceSchDataTables();
    public function getInvoiceB2bByInvId($invb2b_id);
    public function getInvoiceB2bBySchProg($schprog_id);
    public function getInvoiceB2bById($invb2b_num);
    public function deleteInvoiceB2b($invb2b_num);
    public function createInvoiceB2b(array $invoices);
    public function updateInvoiceB2b($invb2b_num, array $invoices);
}

<?php

namespace App\Interfaces;

interface InvoiceDetailRepositoryInterface
{
    public function getInvoiceDetailIdByInvB2b($invb2b_id, $invdtl_installment); 
    public function getInvoiceDetailByInvB2bId($invb2b_id);
    public function deleteInvoiceDetailById($invdtl_id);
    public function createInvoiceDetail(array $installments);
    public function updateInvoiceDetailByInvId($invoiceId, array $installmentDetails);
    public function updateInvoiceDetailByInvB2bId($invb2b_id, array $installments);
}

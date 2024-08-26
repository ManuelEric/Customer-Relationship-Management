<?php

namespace App\Interfaces;

interface InvoiceDetailRepositoryInterface
{
    public function getInvoiceDetailById($identifier);
    public function getInvoiceDetailIdByInvB2b($invb2b_id, $invdtl_installment);
    public function getInvoiceDetailByInvB2bId($invb2b_id);
    public function getInvoiceDetailByInvB2bIdnName($invb2b_id, $name);
    public function getInvoiceDetailByInvIdandName($invoiceId, $name);
    public function getInvoiceDetailByInvId($invoiceId);
    public function deleteInvoiceDetailById($invdtl_id);
    public function deleteInvoiceDetailByinvb2b_Id($invb2b_id);
    public function createOneInvoiceDetail(array $installment);
    public function createInvoiceDetail(array $installments);
    public function updateInvoiceDetailByInvId($invoiceId, array $installmentDetails);
    public function deleteInvoiceDetailByInvId($invoiceId);
    public function updateInvoiceDetailByInvB2bId($invb2b_id, array $installments);
    public function getReportUnpaidInvoiceDetail($start_date, $end_date);
}

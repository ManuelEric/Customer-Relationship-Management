<?php

namespace App\Interfaces;

interface InvoiceProgramRepositoryInterface
{
    public function getAllInvoiceProgramDataTables($status);
    public function getAllDueDateInvoiceProgram(int $days);
    public function getAllInvoiceProgram();
    public function getInvoiceByClientProgId($clientProgId);
    public function getInvoiceByInvoiceId($invoiceId);
    public function createInvoice(array $invoiceDetails);
    public function updateInvoice($invoiceId, array $invoiceDetails);
    public function deleteInvoiceByClientProgId($clientProgId);
    public function getReportInvoiceB2c($start_date, $end_date);
    public function getReportUnpaidInvoiceB2c($start_date, $end_date);
    public function getTotalInvoiceNeeded($monthYear);
    public function getTotalInvoice($monthYear);
    public function getTotalRefundRequest($monthYear);
    public function getInvoiceOutstandingPayment($monthYear, $type, $start_date = null, $end_date = null);
    public function getRevenueByYear($year);
    
    # signature
    public function getInvoicesNeedToBeSigned();

    public function getInvoiceDifferences();
}

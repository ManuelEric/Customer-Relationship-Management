<?php

namespace App\Interfaces;

interface InvoiceProgramRepositoryInterface
{
    # GET
    public function getAllInvoiceProgramDataTables($status);
    public function getAllDueDateInvoiceProgram(int $days);
    public function getAllInvoiceProgram();
    public function getInvoiceByClientProgId($clientProgId);
    public function getInvoiceByInvoiceId($invoiceId);
    public function getReportInvoiceB2c($start_date, $end_date);
    public function getReportUnpaidInvoiceB2c($start_date, $end_date);
    public function getTotalInvoiceNeeded($monthYear);
    public function getTotalInvoice($monthYear);
    public function getTotalRefundRequest($monthYear);
    public function getInvoiceOutstandingPayment($monthYear, $type, $start_date = null, $end_date = null);
    public function getRevenueByYear($year);
    public function getDatatables($model);
    
    //! signature
    public function getInvoicesNeedToBeSigned(bool $dataTables);

    public function getInvoiceDifferences();

    //! collection of invoice bundle functions
    public function getProgramBundle_InvoiceProgram($status);


    # POST
    public function createInvoice(array $invoiceDetails);

    # PATH / PUT
    public function updateInvoice($invoiceId, array $invoiceDetails);

    # DELETE
    public function deleteInvoiceByClientProgId($clientProgId);
}

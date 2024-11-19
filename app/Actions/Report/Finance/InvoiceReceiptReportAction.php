<?php

namespace App\Actions\Report\Finance;

use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;

class InvoiceReceiptReportAction
{
    private InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    private InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    private ReceiptRepositoryInterface $receiptRepository;

    public function __construct(
        InvoiceB2bRepositoryInterface $invoiceB2bRepository,
        InvoiceProgramRepositoryInterface $invoiceProgramRepository,
        ReceiptRepositoryInterface $receiptRepository
        )
    {
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->receiptRepository = $receiptRepository;
    }

    public function execute(Array $incoming_requests)
    {
        $start_date = $incoming_requests['start_date'];
        $end_date = $incoming_requests['end_date'];

        $invoiceB2b = $this->invoiceB2bRepository->getReportInvoiceB2b($start_date, $end_date);
        $invoiceB2c = $this->invoiceProgramRepository->getReportInvoiceB2c($start_date, $end_date);
        $invoices = $invoiceB2c->merge($invoiceB2b);
        $receipts = $this->receiptRepository->getReportReceipt($start_date, $end_date);
        
        
        $total_receipt = $count_invoice = $count_refund = $total_invoice = $total_refund = 0;
        $count_invoice = count($invoiceB2b->where('invb2b_pm', 'Full Payment')) + $invoiceB2b->sum('inv_detail_count');
        $count_invoice += count($invoiceB2c->where('inv_paymentmethod', 'Full Payment')) + $invoiceB2c->sum('invoice_detail_count');
        $total_invoice = $invoiceB2b->sum('invb2b_totpriceidr') + $invoiceB2c->sum('inv_totalprice_idr');
        
        $count_refund = count($invoiceB2b->where('invb2b_status', 2)) + count($invoiceB2c->where('inv_status', 2));
        $total_refund = $invoices->sum('total_refund');

        foreach ($receipts as $receipt) {
            $total_receipt += (int)filter_var($receipt->receipt_amount_idr, FILTER_SANITIZE_NUMBER_INT);
        }

        return compact(
            'invoices',
                'count_invoice',
                'count_refund',
                'total_invoice',
                'total_refund',
                'total_receipt',
                'receipts',
                'start_date',
                'end_date'
        );
    }
}
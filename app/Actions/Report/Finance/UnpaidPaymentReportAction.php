<?php

namespace App\Actions\Report\Finance;

use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;

class UnpaidPaymentReportAction
{
    private InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    private InvoiceProgramRepositoryInterface $invoiceProgramRepository;

    public function __construct(
        InvoiceB2bRepositoryInterface $invoiceB2bRepository,
        InvoiceProgramRepositoryInterface $invoiceProgramRepository
    )
    {
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceProgramRepository = $invoiceProgramRepository;
    }

    public function execute(Array $incoming_requests)
    {        
        $start_date = $incoming_requests['start_date'];
        $end_date = $incoming_requests['end_date'];

        $invoice_b2b = $this->invoiceB2bRepository->getReportUnpaidInvoiceB2b($start_date, $end_date);
        $invoice_b2c = $this->invoiceProgramRepository->getReportUnpaidInvoiceB2c($start_date, $end_date);
        $collection = collect($invoice_b2b);
        $invoice_merge = $collection->merge($invoice_b2c);
        $invoices = $invoice_merge->all();

        $total_amount = $invoice_merge->sum('total_price_inv_idr');

        $total_unpaid = $remaining = $invoice_merge->where('receipt_id', null)->sum('total_price_inv_idr');

        $total_receipt = 0;
        $total_paid = 0;
        $total_diff = 0;
        foreach ($invoices as $invoice) {
            if (isset($invoice->receipt_id)) {
                $total_receipt += $invoice->receipt_amount_idr;
                $total_diff += $invoice->receipt_amount_idr > $invoice->total_price_inv_idr ? $invoice->receipt_amount_idr - $invoice->total_price_inv_idr : 0;
            }
        }

        if ($total_receipt > 0) {
            $total_paid = $total_receipt;
        }

        return compact(
            'invoices',
            'total_amount',
            'total_paid',
            'total_diff',
            'remaining',
            'start_date',
            'end_date',
        );
    }
}
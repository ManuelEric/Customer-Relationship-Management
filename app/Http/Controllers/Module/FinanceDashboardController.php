<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;

class FinanceDashboardController extends Controller
{
    public function __construct($repositories)
    {
        $this->invoiceB2bRepository = $repositories->invoiceB2bRepository;
        $this->invoiceProgramRepository = $repositories->invoiceProgramRepository;
        $this->receiptRepository = $repositories->receiptRepository;
        $this->refundRepository = $repositories->refundRepository;
    }

    public function get($request)
    {
        $totalInvoiceNeededB2b = $this->invoiceB2bRepository->getTotalInvoiceNeeded(date('Y-m'));
        $totalInvoiceNeededB2c = $this->invoiceProgramRepository->getTotalInvoiceNeeded(date('Y-m'));

        $totalInvoiceB2b = $this->invoiceB2bRepository->getTotalInvoice(date('Y-m'));
        $totalInvoiceB2c = $this->invoiceProgramRepository->getTotalInvoice(date('Y-m'));

        // $totalRefundRequestB2b = $this->invoiceB2bRepository->getTotalRefundRequest(date('Y-m'));
        // $totalRefundRequestB2c = $this->invoiceProgramRepository->getTotalRefundRequest(date('Y-m'));

        $totalReceipt = $this->receiptRepository->getTotalReceipt(date('Y-m'));

        $totalInvoiceNeeded = collect($totalInvoiceNeededB2b)->merge($totalInvoiceNeededB2c);

        $totalRefundRequest = $this->refundRepository->getTotalRefundRequest(date('Y-m'));

        $paidPaymentB2b = $this->invoiceB2bRepository->getInvoiceOutstandingPayment(date('Y-m'), 'paid', null, null);
        $paidPaymentB2c = $this->invoiceProgramRepository->getInvoiceOutstandingPayment(date('Y-m'), 'paid');

        $paidPayments = collect($paidPaymentB2b)->merge($paidPaymentB2c);

        $unpaidPaymentB2b = $this->invoiceB2bRepository->getInvoiceOutstandingPayment(date('Y-m'), 'unpaid', null, null);
        $unpaidPaymentB2c = $this->invoiceProgramRepository->getInvoiceOutstandingPayment(date('Y-m'), 'unpaid');

        $unpaidPayments = collect($unpaidPaymentB2b)->merge($unpaidPaymentB2c);

        $totalOutstanding = $unpaidPayments->count();

        $revenueB2b = $this->invoiceB2bRepository->getRevenueByYear(date('Y'));
        $revenueB2c = $this->invoiceProgramRepository->getRevenueByYear(date('Y'));

        $revenue = collect($revenueB2b)->merge($revenueB2c)->groupBy('month')->map(
            function ($row) {
                return $row->sum('total');
            }
        );

        $totalInvoice[0] = [
            'count_invoice' => count($totalInvoiceB2b) + count($totalInvoiceB2b),
            'total' => $totalInvoiceB2b->where('invb2b_pm', 'Full Payment')->sum('invb2b_totpriceidr') + $totalInvoiceB2b->where('invb2b_pm', 'Installment')->sum('invdtl_amountidr')
        ];

        $totalInvoice[1] = [
            'count_invoice' => count($totalInvoiceB2c),
            'total' => $totalInvoiceB2c->where('inv_paymentmethod', 'Full Payment')->sum('inv_totalprice_idr') + $totalInvoiceB2c->where('inv_paymentmethod', 'Installment')->sum('invdtl_amountidr')
        ];

        return [
            'invoiceNeededToday' => count($totalInvoiceNeeded->where('success_date', date('Y-m-d'))),
            'outstandingToday' => count($unpaidPayments->where('invoice_duedate', date('Y-m-d', strtotime("-3 days")))),
            'refundRequestToday' => count($totalRefundRequest->where('refund_date', date('Y-m-d'))),
            'totalInvoiceNeeded' => count($totalInvoiceNeeded),
            'totalInvoice' => $totalInvoice,
            'totalReceipt' => $totalReceipt,
            'totalRefundRequest' => count($totalRefundRequest),
            'paidPayments' => $paidPayments,
            'unpaidPayments' => $unpaidPayments,
            'totalOutstanding' => $totalOutstanding,
            'revenue' => $revenue,
        ];
    }
}

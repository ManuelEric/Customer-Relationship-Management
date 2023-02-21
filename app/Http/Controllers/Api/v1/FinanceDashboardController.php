<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;

class FinanceDashboardController extends Controller
{
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    protected ReceiptRepositoryInterface $receiptRepository;


    public function __construct(InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceProgramRepositoryInterface $invoiceProgramRepository, ReceiptRepositoryInterface $receiptRepository)
    {
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->receiptRepository = $receiptRepository;
    }


    public function getTotalByMonth(Request $request)
    {
        $monthYear = $request->route('month');

        $totalInvoiceNeededB2b = $this->invoiceB2bRepository->getTotalInvoiceNeeded($monthYear);
        $totalInvoiceNeededB2c = $this->invoiceProgramRepository->getTotalInvoiceNeeded($monthYear);

        $totalInvoiceB2b = $this->invoiceB2bRepository->getTotalInvoice($monthYear);
        $totalInvoiceB2c = $this->invoiceProgramRepository->getTotalInvoice($monthYear);

        $totalRefundRequestB2b = $this->invoiceB2bRepository->getTotalRefundRequest($monthYear);
        $totalRefundRequestB2c = $this->invoiceProgramRepository->getTotalRefundRequest($monthYear);

        $totalReceipt = $this->receiptRepository->getTotalReceipt($monthYear);

        $totalInvoiceNeeded = collect($totalInvoiceNeededB2b)->merge($totalInvoiceNeededB2c)->sum('count_invoice_needed');
        $totalInvoice = collect($totalInvoiceB2b)->merge($totalInvoiceB2c);

        $totalRefundRequest = collect($totalRefundRequestB2b)->merge($totalRefundRequestB2c)->sum('count_refund_request');

        $unpaidPaymentB2b = $this->invoiceB2bRepository->getInvoiceOutstandingPayment($monthYear, 'unpaid', null, null);
        $unpaidPaymentB2c = $this->invoiceProgramRepository->getInvoiceOutstandingPayment($monthYear, 'unpaid');

        $unpaidPayments = collect($unpaidPaymentB2b)->merge($unpaidPaymentB2c);

        $totalOutstanding = $unpaidPayments->count();


        $data = [
            'totalInvoiceNeeded' => $totalInvoiceNeeded,
            'totalInvoice' => $totalInvoice,
            'totalReceipt' => $totalReceipt,
            'totalRefundRequest' => $totalRefundRequest,
            'totalOutstanding' => $totalOutstanding,
        ];

        if ($data) {
            $response = [
                'success' => true,
                'data' => $data
            ];
        } else {
            $response = [
                'success' => false,
                'data' => null
            ];
        }

        return response()->json($response);
    }

    public function getOutstandingPayment(Request $request)
    {
        $monthYear = $request->route('month');

        $paidPaymentB2b = $this->invoiceB2bRepository->getInvoiceOutstandingPayment($monthYear, 'paid');
        $paidPaymentB2c = $this->invoiceProgramRepository->getInvoiceOutstandingPayment($monthYear, 'paid');

        $paidPayments = collect($paidPaymentB2b)->merge($paidPaymentB2c);

        $unpaidPaymentB2b = $this->invoiceB2bRepository->getInvoiceOutstandingPayment($monthYear, 'unpaid');
        $unpaidPaymentB2c = $this->invoiceProgramRepository->getInvoiceOutstandingPayment($monthYear, 'unpaid');

        $unpaidPayments = collect($unpaidPaymentB2b)->merge($unpaidPaymentB2c);

        $data = [
            'paidPayments' => $paidPayments,
            'unpaidPayments' => $unpaidPayments,
        ];

        if ($data) {
            $response = [
                'success' => true,
                'data' => $data
            ];
        } else {
            $response = [
                'success' => false,
                'data' => null
            ];
        }

        return response()->json($response);
    }

    public function getOutstandingPaymentByPeriod(Request $request)
    {
        $start_date = $request->route('start_date');
        $end_date = $request->route('end_date');
        // $monthYear = $request->route('month');

        $paidPaymentB2b = $this->invoiceB2bRepository->getInvoiceOutstandingPayment(null, 'paid', $start_date, $end_date);
        $paidPaymentB2c = $this->invoiceProgramRepository->getInvoiceOutstandingPayment(null, 'paid', $start_date, $end_date);

        $paidPayments = collect($paidPaymentB2b)->merge($paidPaymentB2c);

        $unpaidPaymentB2b = $this->invoiceB2bRepository->getInvoiceOutstandingPayment(null, 'unpaid', $start_date, $end_date);
        $unpaidPaymentB2c = $this->invoiceProgramRepository->getInvoiceOutstandingPayment(null, 'unpaid', $start_date, $end_date);

        $unpaidPayments = collect($unpaidPaymentB2b)->merge($unpaidPaymentB2c);

        $data = [
            'paidPayments' => $paidPayments,
            'unpaidPayments' => $unpaidPayments,
        ];

        if ($data) {
            $response = [
                'success' => true,
                'data' => $data
            ];
        } else {
            $response = [
                'success' => false,
                'data' => null
            ];
        }

        return response()->json($response);
    }

    public function getRevenueByYear(Request $request)
    {
        $year = $request->route('year');

        $revenueB2b = $this->invoiceB2bRepository->getRevenueByYear($year);
        $revenueB2c = $this->invoiceProgramRepository->getRevenueByYear($year);

        $revenue = collect($revenueB2b)->merge($revenueB2c)->groupBy('month')->map(
            function ($row) {
                return $row->sum('total');
            }
        );

        $data = [
            'totalRevenue' => $revenue,
            // 'unpaidPayments' => $unpaidPayments,
        ];

        if ($data) {
            $response = [
                'success' => true,
                'data' => $data
            ];
        } else {
            $response = [
                'success' => false,
                'data' => null
            ];
        }

        return response()->json($response);
    }

    public function getRevenueDetailByMonth(Request $request)
    {
        $monthYear = $request->route('year') . '-' . $request->route('month');


        $paidPaymentB2b = $this->invoiceB2bRepository->getInvoiceOutstandingPayment($monthYear, 'paid');
        $paidPaymentB2c = $this->invoiceProgramRepository->getInvoiceOutstandingPayment($monthYear, 'paid');

        $paidPayments = collect($paidPaymentB2b)->merge($paidPaymentB2c);


        $data = [
            'revenueDetail' => $paidPayments,
            // 'unpaidPayments' => $unpaidPayments,
        ];

        if ($data) {
            $response = [
                'success' => true,
                'data' => $data
            ];
        } else {
            $response = [
                'success' => false,
                'data' => null
            ];
        }

        return response()->json($response);
    }
}

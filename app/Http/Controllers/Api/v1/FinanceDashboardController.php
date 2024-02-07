<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\RefundRepositoryInterface;

class FinanceDashboardController extends Controller
{
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected RefundRepositoryInterface $refundRepository;


    public function __construct(InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceProgramRepositoryInterface $invoiceProgramRepository, ReceiptRepositoryInterface $receiptRepository, RefundRepositoryInterface $refundRepository)
    {
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->receiptRepository = $receiptRepository;
        $this->refundRepository = $refundRepository;
    }


    public function getTotalByMonth(Request $request)
    {
        $monthYear = $request->route('month');

        $totalInvoiceNeededB2b = $this->invoiceB2bRepository->getTotalInvoiceNeeded($monthYear);
        $totalInvoiceNeededB2c = $this->invoiceProgramRepository->getTotalInvoiceNeeded($monthYear);

        $totalInvoiceB2b = $this->invoiceB2bRepository->getTotalInvoice($monthYear);
        $totalInvoiceB2c = $this->invoiceProgramRepository->getTotalInvoice($monthYear);

        // $totalRefundRequestB2b = $this->invoiceB2bRepository->getTotalRefundRequest($monthYear);
        // $totalRefundRequestB2c = $this->invoiceProgramRepository->getTotalRefundRequest($monthYear);

        $totalReceipt = $this->receiptRepository->getTotalReceipt($monthYear);

        $totalInvoiceNeeded = collect($totalInvoiceNeededB2b)->merge($totalInvoiceNeededB2c);

        $totalRefundRequest = $this->refundRepository->getTotalRefundRequest($monthYear);

        $unpaidPaymentB2b = $this->invoiceB2bRepository->getInvoiceOutstandingPayment($monthYear, 'unpaid');
        $unpaidPaymentB2c = $this->invoiceProgramRepository->getInvoiceOutstandingPayment($monthYear, 'unpaid');

        $unpaidPayments = collect($unpaidPaymentB2b)->merge($unpaidPaymentB2c);

        $totalOutstanding = $unpaidPayments->count();

        $totalInvoice[0] = [
            'count_invoice' => count($totalInvoiceB2b) + count($totalInvoiceB2b),
            'total' => $totalInvoiceB2b->where('invb2b_pm', 'Full Payment')->sum('invb2b_totpriceidr') + $totalInvoiceB2b->where('invb2b_pm', 'Installment')->sum('invdtl_amountidr')
        ];

        $totalInvoice[1] = [
            'count_invoice' => count($totalInvoiceB2c),
            'total' => $totalInvoiceB2c->where('inv_paymentmethod', 'Full Payment')->sum('inv_totalprice_idr') + $totalInvoiceB2c->where('inv_paymentmethod', 'Installment')->sum('invdtl_amountidr')
        ];

        $data = [
            'invoiceNeededToday' => count($totalInvoiceNeeded->where('success_date', date('Y-m-d'))),
            'outstandingToday' => $unpaidPayments->where('invoice_duedate', date('Y-m-d', strtotime("-3 days"))),
            'refundRequestToday' => $totalRefundRequest->where('refund_date', date('Y-m-d')),
            'totalInvoiceNeeded' => count($totalInvoiceNeeded),
            'totalInvoice' => $totalInvoice,
            'totalReceipt' => $totalReceipt,
            'totalRefundRequest' => count($totalRefundRequest),
            'totalOutstanding' => $totalOutstanding,
            'monthYear' => $monthYear,

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

    public function getFinanceDetailByMonth(Request $request)
    {
        $monthYear = $request->route('month');
        $type = $request->route('type');

        $index = 1;
        $html = '';
        $reminder = null;

        switch ($type) {
            case 'invoice-needed':
                $invoiceNeededB2b = $this->invoiceB2bRepository->getTotalInvoiceNeeded($monthYear);
                $invoiceNeededB2c = $this->invoiceProgramRepository->getTotalInvoiceNeeded($monthYear);

                $invoiceNeeded = collect($invoiceNeededB2b)->merge($invoiceNeededB2c);
                if ($invoiceNeeded->count() == 0)
                    return response()->json(['title' => 'List of ' . ucwords(str_replace('-', ' ', $type)), 'html_ctx' => '<tr align="center"><td colspan="5">No ' . str_replace('-', ' ', $type) . ' data</td></tr>']);

                foreach ($invoiceNeeded->sortBy('success_date') as $inv) {

                    $html .= '<tr' . ($inv->success_date == date('Y-m-d') ?  ' class="table-danger detail"' : ' class="detail"') . ' data-clientprog="' . $inv->client_prog_id . '" data-typeprog="' . $inv->type . '" data-type="invoice-needed" style="cursor:pointer">
                        <td>' . $index++ . '</td>
                        <td>' . $inv->client_name . '</td>
                        <td>' . $inv->program_name . '</td>
                        <td>' . date('M d, Y', strtotime($inv->success_date)) . '</td>
                        <td>' . $inv->pic_name . '</td>
                    </tr>';
                }
                break;

            case 'outstanding':
                $unpaidPaymentB2b = $this->invoiceB2bRepository->getInvoiceOutstandingPayment($monthYear, 'unpaid');
                $unpaidPaymentB2c = $this->invoiceProgramRepository->getInvoiceOutstandingPayment($monthYear, 'unpaid');

                $unpaidPayments = collect($unpaidPaymentB2b)->merge($unpaidPaymentB2c);
                if ($unpaidPayments->count() == 0)
                    return response()->json(['title' => 'List of ' . ucwords(str_replace('-', ' ', $type)), 'html_ctx' => '<tr align="center"><td colspan="8">No ' . str_replace('-', ' ', $type) . ' data</td></tr>']);

                foreach ($unpaidPayments->sortBy('invoice_duedate') as $unpaidPayment) {

                    if ($unpaidPayment->typeprog == 'client_prog') {
                        $reminder[$unpaidPayment->client_id] = [
                            'parent_fullname' => $unpaidPayment->parent_name,
                            'child_phone' => $unpaidPayment->child_phone,
                            'parent_phone' => $unpaidPayment->parent_phone,
                            'program_name' => $unpaidPayment->program_name,
                            'invoice_duedate' => $unpaidPayment->invoice_duedate,
                            'total_payment' => $unpaidPayment->total,
                            'clientprog_id' => $unpaidPayment->client_prog_id,
                            'payment_method' => (isset($unpaidPayment->installment_name)) ? ' ' . $unpaidPayment->installment_name  : '',
                            'parent_id' => $unpaidPayment->parent_id,
                            'client_id' => $unpaidPayment->client_id,
                            'parents' => isset($unpaidPayment->clientprog->client->parents) ? $unpaidPayment->clientprog->client->parents : []
                        ];
                    }

                    $html .= '<tr' . ($unpaidPayment->invoice_duedate == date('Y-m-d', strtotime("-3 days")) ?  ' class="table-danger"' : ' class="a"') . ' style="cursor:pointer">
                            <td class="detail" data-clientprog="' . $unpaidPayment->client_prog_id . '" data-typeprog="' . $unpaidPayment->typeprog . '" data-invid="' . $unpaidPayment->invoice_id . '" data-type="outstanding">' . $index++ . '</td>
                            <td class="detail" data-clientprog="' . $unpaidPayment->client_prog_id . '" data-typeprog="' . $unpaidPayment->typeprog . '" data-invid="' . $unpaidPayment->invoice_id . '" data-type="outstanding">' . $unpaidPayment->full_name . '</td>
                            <td class="reminder text-center" data-clientid="' . $unpaidPayment->client_id .  '">' . ($unpaidPayment->typeprog == 'client_prog' ? '<button data-bs-toggle="modal" data-bs-target="#reminderModal" class="mx-1 btn btn-sm btn-outline-success reminder"><i class="bi bi-whatsapp"></i></button>' : '-') . '</td>
                            <td class="detail" data-clientprog="' . $unpaidPayment->client_prog_id . '" data-typeprog="' . $unpaidPayment->typeprog . '" data-invid="' . $unpaidPayment->invoice_id . '" data-type="outstanding">' . $unpaidPayment->invoice_id . '</td>
                            <td class="detail" data-clientprog="' . $unpaidPayment->client_prog_id . '" data-typeprog="' . $unpaidPayment->typeprog . '" data-invid="' . $unpaidPayment->invoice_id . '" data-type="outstanding">' . $unpaidPayment->type . '</td>
                            <td class="detail" data-clientprog="' . $unpaidPayment->client_prog_id . '" data-typeprog="' . $unpaidPayment->typeprog . '" data-invid="' . $unpaidPayment->invoice_id . '" data-type="outstanding">' . $unpaidPayment->program_name . '</td>
                            <td class="detail" data-clientprog="' . $unpaidPayment->client_prog_id . '" data-typeprog="' . $unpaidPayment->typeprog . '" data-invid="' . $unpaidPayment->invoice_id . '" data-type="outstanding">' . (isset($unpaidPayment->installment_name) ? $unpaidPayment->installment_name : '-') . '</td>
                            <td class="detail" data-clientprog="' . $unpaidPayment->client_prog_id . '" data-typeprog="' . $unpaidPayment->typeprog . '" data-invid="' . $unpaidPayment->invoice_id . '" data-type="outstanding">' . date('M d, Y', strtotime($unpaidPayment->invoice_duedate)) . '</td>
                            <td class="detail" data-clientprog="' . $unpaidPayment->client_prog_id . '" data-typeprog="' . $unpaidPayment->typeprog . '" data-invid="' . $unpaidPayment->invoice_id . '" data-type="outstanding"> Rp. ' . number_format($unpaidPayment->total) . '</td>
                        </tr.>';
                }
                break;

            case 'refund-request':
                $refundRequest = $this->refundRepository->getTotalRefundRequest($monthYear);
                if ($refundRequest->count() == 0)
                    return response()->json(['title' => 'List of ' . ucwords(str_replace('-', ' ', $type)), 'html_ctx' => '<tr align="center"><td colspan="6">No ' . str_replace('-', ' ', $type) . ' data</td></tr>']);

                foreach ($refundRequest->sortBy('refund_date') as $refund_req) {

                    $html .= '<tr' . ($refund_req->refund_date == date('Y-m-d') ?  ' class="table-danger detail"' : ' class="detail"') . ' data-clientprog="' . $refund_req->client_prog_id . '" data-typeprog="' . $refund_req->typeprog . '" data-invid="' . $refund_req->invoice_id . '" data-type="refund-request" style="cursor:pointer">
                            <td>' . $index++ . '</td>
                            <td>' . $refund_req->client_fullname . '</td>
                            <td>' . $refund_req->receipt_id . '</td>
                            <td>' . $refund_req->program_name . '</td>
                            <td>' . date('M d, Y', strtotime($refund_req->refund_date)) . '</td>
                            <td>' . $refund_req->pic_name . '</td>
                        </tr>';
                }
                break;
        }

        return response()->json(
            [
                'title' => 'List of ' . ucwords($type),
                'html_ctx' => $html,
                'reminder' => $reminder
            ]
        );
    }
}

<?php

namespace App\Http\Controllers;

use App\Actions\Report\Partnership\PartnershipReportAction;
use App\Actions\Report\Sales\EventReportAction;
use App\Actions\Report\Sales\SalesReportAction;
use App\Http\Requests\ReportEventRequest;
use App\Http\Requests\ReportPartnershipRequest;
use App\Http\Requests\ReportSalesRequest;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\SchoolVisitRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    protected ClientEventRepositoryInterface $clientEventRepository;
    protected EventRepositoryInterface $eventRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected PartnerProgramRepositoryInterface $partnerProgramRepository;
    protected SchoolRepositoryInterface $schoolRepository;
    protected CorporateRepositoryInterface $corporateRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected SchoolVisitRepositoryInterface $schoolVisitRepository;
    protected InvoiceDetailRepositoryInterface $invoiceDetailRepository;
    protected ReferralRepositoryInterface $referralRepository;

    protected ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(
        ClientEventRepositoryInterface $clientEventRepository,
        EventRepositoryInterface $eventRepository,
        SchoolProgramRepositoryInterface $schoolProgramRepository,
        PartnerProgramRepositoryInterface $partnerProgramRepository,
        SchoolRepositoryInterface $schoolRepository,
        CorporateRepositoryInterface $corporateRepository,
        UniversityRepositoryInterface $universityRepository,
        InvoiceB2bRepositoryInterface $invoiceB2bRepository,
        InvoiceProgramRepositoryInterface $invoiceProgramRepository,
        ReceiptRepositoryInterface $receiptRepository,
        SchoolVisitRepositoryInterface $schoolVisitRepository,
        InvoiceDetailRepositoryInterface $invoiceDetailRepository,
        ReferralRepositoryInterface $referralRepository,
        ClientProgramRepositoryInterface $clientProgramRepository
    ) {
        $this->clientEventRepository = $clientEventRepository;
        $this->eventRepository = $eventRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->partnerProgramRepository = $partnerProgramRepository;
        $this->schoolRepository = $schoolRepository;
        $this->corporateRepository = $corporateRepository;
        $this->universityRepository = $universityRepository;
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->receiptRepository = $receiptRepository;
        $this->schoolVisitRepository = $schoolVisitRepository;
        $this->invoiceDetailRepository = $invoiceDetailRepository;
        $this->referralRepository = $referralRepository;
        $this->clientProgramRepository = $clientProgramRepository;
    }

    /**
     * Sales tracking
     */
    public function fnSalesTracking(
        ReportSalesRequest $request,
        SalesReportAction $salesReportAction,
    ) 
    {
        # initialize
        $validated = $request->safe()->only([
            'start', 
            'end',
            'main',
            'program',
            'pic',
        ]);

        $sales_report = $salesReportAction->execute($validated);
        return view('pages.report.sales-tracking.index')->with($sales_report);
    }

    public function fnEventTracking(
        ReportEventRequest $request,
        EventReportAction $eventReportAction,
        )
    {
        # initialize
        $filter = $request->safe()->only([
            'event_name',
            'start_date',
            'end_date'
        ]);

        if ($request->ajax()) 
            return $this->clientEventRepository->getAllClientEventDataTables($filter);
        

        $event_tracking = $eventReportAction->execute($filter['event_name']);
        return view('pages.report.event-tracking.index')->with($event_tracking);
    }

    public function fnPartnershipReport(
        ReportPartnershipRequest $request,
        PartnershipReportAction $partnershipReportAction,
        )
    {
        $validated = $request->safe()->only(['start_date', 'end_date']);
        $partnership_report = $partnershipReportAction->execute($validated);
        return view('pages.report.partnership.index')->with($partnership_report);
    }

    public function invoice_receipt(Request $request)
    {
        $start_date = null;
        $end_date = null;

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $invoiceB2b = $this->invoiceB2bRepository->getReportInvoiceB2b($start_date, $end_date);
        $invoiceB2c = $this->invoiceProgramRepository->getReportInvoiceB2c($start_date, $end_date);
        $invoices = $invoiceB2c->merge($invoiceB2b);
        $receipts = $this->receiptRepository->getReportReceipt($start_date, $end_date);

        $totalReceipt = 0;
        $countInvoice = 0;
        $countRefund = 0;
        $totalInvoice = 0;
        $totalRefund = 0;

        // $data_receipts = $receipts->filter(function ($item) {
        //     // Return true if you want this item included in the resultant collection
        //     return $item->status_where == 1 || $item->referral_type == 'Out';
        // });


        $countInvoice = count($invoiceB2b->where('invb2b_pm', 'Full Payment')) + $invoiceB2b->sum('inv_detail_count');
        $countInvoice += count($invoiceB2c->where('inv_paymentmethod', 'Full Payment')) + $invoiceB2c->sum('invoice_detail_count');
        $totalInvoice = $invoiceB2b->sum('invb2b_totpriceidr') + $invoiceB2c->sum('inv_totalprice_idr');
        
        $countRefund = count($invoiceB2b->where('invb2b_status', 2)) + count($invoiceB2c->where('inv_status', 2));
        $totalRefund = $invoices->sum('total_refund');

        foreach ($receipts as $receipt) {
            $totalReceipt += (int)filter_var($receipt->receipt_amount_idr, FILTER_SANITIZE_NUMBER_INT);
        }

        return view('pages.report.invoice.index')->with(
            [
                'invoices' => $invoices,
                'countInvoice' => $countInvoice,
                'countRefund' => $countRefund,
                'totalInvoice' => $totalInvoice,
                'totalRefund' => $totalRefund,
                'totalReceipt' => $totalReceipt,
                'receipts' => $receipts,
            ]
        );
    }

    public function unpaid_payment(Request $request)
    {
        $start_date = null;
        $end_date = null;
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');


        $invoiceB2b = $this->invoiceB2bRepository->getReportUnpaidInvoiceB2b($start_date, $end_date);
        $invoiceB2c = $this->invoiceProgramRepository->getReportUnpaidInvoiceB2c($start_date, $end_date);
        $collection = collect($invoiceB2b);
        $invoiceMerge = $collection->merge($invoiceB2c);
        $invoices = $invoiceMerge->all();

        $totalAmount = $invoiceMerge->sum('total_price_inv_idr');

        $totalUnpaid = $invoiceMerge->where('receipt_id', null)->sum('total_price_inv_idr');

        $totalReceipt = 0;
        $totalPaid = 0;
        $totalDiff = 0;
        foreach ($invoices as $invoice) {
            if (isset($invoice->receipt_id)) {
                $totalReceipt += $invoice->receipt_amount_idr;
                $totalDiff += $invoice->receipt_amount_idr > $invoice->total_price_inv_idr ? $invoice->receipt_amount_idr - $invoice->total_price_inv_idr : 0;
            }
        }

        if ($totalReceipt > 0) {
            $totalPaid = $totalReceipt;
        }


        return view('pages.report.unpaid-payment.index')->with(
            [
                'invoices' => $invoices,
                'totalAmount' => $totalAmount,
                'totalPaid' => $totalPaid,
                'totalDiff' => $totalDiff,
                'remaining' => $totalUnpaid
            ]
        );
    }

    public function program_tracking(Request $request)
    {
        $start_month = $request->get('start_month') ?? date('Y-m');
        $end_month = $request->get('end_month') ?? date('Y-m');

        try {
            $programTracking = $this->invoiceProgramRepository->getProgramTracker($start_month, $end_month);
        } catch (Exception $e) {
            Log::error('Failed get data program tracking : ' . $e->getMessage() . ' | On Line: ' . $e->getLine());
        }

        return view('pages.report.program-tracking.index')->with(
            [
                'programTracking' => $programTracking,
            ]
        );

    }

    protected function getAllDataClient($data, $type)
    {
        $dataClient =  new Collection();
        foreach ($data as $d) {
            $dataClient->push((object)[
                'type' => $type,
                'client_id' => $d->client_id,
                'role_name' => $d->role_name
            ]);
        }

        return $dataClient;
    }
}

<?php

namespace App\Http\Controllers;

use App\Actions\Report\Finance\InvoiceReceiptReportAction;
use App\Actions\Report\Finance\UnpaidPaymentReportAction;
use App\Actions\Report\Partnership\PartnershipReportAction;
use App\Actions\Report\Sales\EventReportAction;
use App\Actions\Report\Sales\LeadTrackerReportAction;
use App\Actions\Report\Sales\SalesReportAction;
use App\Enum\LogModule;
use App\Http\Requests\ReportEventRequest;
use App\Http\Requests\ReportInvoiceReceiptRequest;
use App\Http\Requests\ReportPartnershipRequest;
use App\Http\Requests\ReportProgramTrackingRequest;
use App\Http\Requests\ReportSalesRequest;
use App\Http\Requests\ReportUnpaidPaymentRequest;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Services\Log\LogService;
use Illuminate\Support\Collection;
use Exception;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ClientEventRepositoryInterface $clientEventRepository;
    protected InvoiceProgramRepositoryInterface $invoiceProgramRepository;


    public function __construct(
        ClientEventRepositoryInterface $clientEventRepository,
        InvoiceProgramRepositoryInterface $invoiceProgramRepository,
    ) {
        $this->clientEventRepository = $clientEventRepository;
        $this->invoiceProgramRepository = $invoiceProgramRepository;
    }

    /**
     * Sales tracking
     */
    public function fnSalesTracking(
        ReportSalesRequest $request,
        SalesReportAction $salesReportAction,
        LogService $log_service
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
        try {
            $sales_report = $salesReportAction->execute($validated);
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::REPORT_SALES_TRACKING, $e->getMessage(), $e->getLine(), $e->getFile());
        }
        $log_service->createInfoLog(LogModule::REPORT_SALES_TRACKING, 'User accessed report sales tracking');
        return view('pages.report.sales-tracking.index')->with($sales_report);
    }

    public function fnEventTracking(
        ReportEventRequest $request,
        EventReportAction $eventReportAction,
        LogService $log_service,
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
        
        try {
            $event_tracking = $eventReportAction->execute($filter['event_name']);
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::REPORT_EVENT_TRACKING, $e->getMessage(), $e->getLine(), $e->getFile());
        }
        $log_service->createInfoLog(LogModule::REPORT_EVENT_TRACKING, 'User accessed report event tracking');
        return view('pages.report.event-tracking.index')->with($event_tracking);
    }

    public function fnPartnershipReport(
        ReportPartnershipRequest $request,
        PartnershipReportAction $partnershipReportAction,
        LogService $log_service,
        )
    {
        $validated = $request->safe()->only(['start_date', 'end_date']);
        try {
            $partnership_report = $partnershipReportAction->execute($validated);
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::REPORT_PARTNERSHIP, $e->getMessage(), $e->getLine(), $e->getFile());
        }
        $log_service->createInfoLog(LogModule::REPORT_PARTNERSHIP, 'User accessed report partnership');
        return view('pages.report.partnership.index')->with($partnership_report);
    }

    public function fnInvoiceReceiptReport(
        ReportInvoiceReceiptRequest $request,
        InvoiceReceiptReportAction $invoiceReceiptReportAction,
        LogService $log_service,
        )
    {
        $validated = $request->safe()->only(['start_date', 'end_date']);
        try {
            $invoice_receipt_report = $invoiceReceiptReportAction->execute($validated);
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::REPORT_INVOICE_RECEIPT, $e->getMessage(), $e->getLine(), $e->getFile());
        }
        $log_service->createInfoLog(LogModule::REPORT_INVOICE_RECEIPT, 'User accessed report invoice receipt');
        return view('pages.report.invoice.index')->with($invoice_receipt_report);
    }

    public function fnUnpaidPaymentReport(
        ReportUnpaidPaymentRequest $request,
        UnpaidPaymentReportAction $unpaidPaymentReportAction,
        LogService $log_service,
        )
    {
        $validated = $request->safe()->only(['start_date', 'end_date']);
        try {
            $unpaid_payment_report = $unpaidPaymentReportAction->execute($validated);
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::REPORT_UNPAID_PAYMENT, $e->getMessage(), $e->getLine(), $e->getFile());
        }
        $log_service->createInfoLog(LogModule::REPORT_UNPAID_PAYMENT, 'User accessed report unpaid payment');
        return view('pages.report.unpaid-payment.index')->with($unpaid_payment_report);
    }

    public function fnProgramTracking(
        ReportProgramTrackingRequest $request,
        LogService $log_service
        )
    {
        $validated = $request->safe()->only(['start_month', 'end_month']);
        $start_month = $validated['start_month'];
        $end_month = $validated['end_month'];
        try {
            $program_tracking = $this->invoiceProgramRepository->getProgramTracker($start_month, $end_month);
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::REPORT_PROGRAM_TRACKING, $e->getMessage(), $e->getLine(), $e->getFile());
        }
        $log_service->createInfoLog(LogModule::REPORT_PROGRAM_TRACKING, 'User accessed report program tracking');
        return view('pages.report.program-tracking.index')->with(compact('program_tracking'));
    }

    public function fnLeadTracking(
        Request $request,
        LeadTrackerReportAction $leadTrackerReportAction,
        )
    {
        $date_range = $request->get('daterange');
        $lead_tracker_report = $leadTrackerReportAction->execute($date_range);
        return view('pages.report.lead.index')->with($lead_tracker_report);
    }
}

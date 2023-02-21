<?php

namespace App\Http\Controllers;


use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\SchoolVisitRepositoryInterface;
use App\Interfaces\InvoiceDetailRepositoryInterface;
use Illuminate\Http\Request;

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
        InvoiceDetailRepositoryInterface $invoiceDetailRepository
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
    }

    public function event(Request $request)
    {
        $eventId = null;
        if ($request->get('event_id') != null) {
            $eventId = $request->get('event_id');
        }

        $events = $this->eventRepository->getAllEvents();
        $clientEvents = $this->clientEventRepository->getReportClientEvents($eventId);
        $clients = $this->clientEventRepository->getReportClientEventsGroupByRoles($eventId);
        $conversionLeads = $this->clientEventRepository->getConversionLead($eventId);

        return view('pages.report.event-tracking.index')->with(
            [
                'clientEvents' => $clientEvents,
                'clients' => $clients,
                'events' => $events,
                'conversionLeads' => $conversionLeads,
            ]
        );
    }

    public function partnership(Request $request)
    {
        $start_date = null;
        $end_date = null;

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');


        $partnerPrograms = $this->partnerProgramRepository->getReportPartnerPrograms($start_date, $end_date);
        $schoolPrograms = $this->schoolProgramRepository->getReportSchoolPrograms($start_date, $end_date);
        $schools = $this->schoolRepository->getReportNewSchool($start_date, $end_date);
        $schoolVisits = $this->schoolVisitRepository->getReportSchoolVisit($start_date, $end_date);
        $partners = $this->corporateRepository->getReportNewPartner($start_date, $end_date);
        $universities = $this->universityRepository->getReportNewUniversity($start_date, $end_date);

        return view('pages.report.partnership.index')->with(
            [
                'partnerPrograms' => $partnerPrograms,
                'schoolPrograms' => $schoolPrograms,
                'schools' => $schools,
                'schoolVisits' => $schoolVisits,
                'partners' => $partners,
                'universities' => $universities,
            ]
        );
    }

    public function invoice_receipt(Request $request)
    {
        $start_date = null;
        $end_date = null;

        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $invoiceB2b = $this->invoiceB2bRepository->getReportInvoiceB2b($start_date, $end_date, 'created_at');
        $invoiceB2c = $this->invoiceProgramRepository->getReportInvoiceB2c($start_date, $end_date, 'created_at');
        $invoices = $invoiceB2c->merge($invoiceB2b);
        $receipts = $this->receiptRepository->getReportReceipt($start_date, $end_date);

        $totalReceipt = 0;
        foreach ($receipts as $receipt) {
            $totalReceipt += (int)filter_var($receipt->receipt_amount_idr, FILTER_SANITIZE_NUMBER_INT);
        }

        if ($totalReceipt > 0) {
            $totalReceipt = substr($totalReceipt, 0, -2);
        }

        return view('pages.report.invoice.index')->with(
            [
                'invoices' => $invoices,
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

        $invoiceB2bReport = $this->invoiceB2bRepository->getReportInvoiceB2b($start_date, $end_date, 'invb2b_duedate');
        $invoiceB2cReport = $this->invoiceProgramRepository->getReportInvoiceB2c($start_date, $end_date, 'inv_duedate');

        $totalAmount = $invoiceB2bReport->sum('invb2b_totpriceidr') + $invoiceB2cReport->sum('inv_totalprice_idr');

        $totalUnpaid = $invoiceMerge->where('receipt_id', null)->sum('total_price_inv');

        $totalReceipt = 0;
        $totalPaid = 0;
        $totalDiff = 0;
        foreach ($invoices as $invoice) {
            if (isset($invoice->receipt_id)) {
                $totalReceipt += $invoice->receipt_amount_idr;
                $totalDiff += $invoice->receipt_amount_idr > $invoice->total_price_inv ? $invoice->receipt_amount_idr - $invoice->total_price_inv : 0;
            }
        }

        if ($totalReceipt > 0) {
            $totalPaid = $totalReceipt;
        }

        // return $totalDiff;
        // exit;

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
}

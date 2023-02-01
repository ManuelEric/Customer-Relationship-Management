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

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
        SchoolVisitRepositoryInterface $schoolVisitRepository
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
    }

    public function event(Request $request)
    {
        $eventId = null;
        if ($request->get('event_id') != null) {
            $eventId = $request->get('event_id');
        }

        $events = $this->eventRepository->getAllEvents();
        $clientEvents = $this->clientEventRepository->getAllClientEvents($eventId);
        $clients = $this->clientEventRepository->getAllClientEventsGroupByRoles($eventId);
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

        if ($request->get('start_date') != null) {
            $start_date = $request->get('start_date');
        } else if ($request->get('end_date') != null) {
            $end_date = $request->get('end_date');
        } else if ($request->get('start_date') != null && $request->get('end_date') != null) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
        }


        $partnerPrograms = $this->partnerProgramRepository->getReportPartnerPrograms($start_date, $end_date);
        $schoolPrograms = $this->schoolProgramRepository->getReportSchoolPrograms($start_date, $end_date);
        $schools = $this->schoolRepository->getReportNewSchool($start_date, $end_date);
        $partners = $this->corporateRepository->getReportNewPartner($start_date, $end_date);
        $universities = $this->universityRepository->getReportNewUniversity($start_date, $end_date);

        return view('pages.report.partnership.index')->with(
            [
                'partnerPrograms' => $partnerPrograms,
                'schoolPrograms' => $schoolPrograms,
                'schools' => $schools,
                'partners' => $partners,
                'universities' => $universities,
            ]
        );
    }

    public function invoice_receipt(Request $request)
    {
        $start_date = null;
        $end_date = null;

        if ($request->get('start_date') != null) {
            $start_date = $request->get('start_date');
        } else if ($request->get('end_date') != null) {
            $end_date = $request->get('end_date');
        } else if ($request->get('start_date') != null && $request->get('end_date') != null) {
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
        }

        $invoiceB2b = $this->invoiceB2bRepository->getReportInvoiceB2b($start_date, $end_date);
        $invoiceB2c = $this->invoiceProgramRepository->getReportInvoiceB2c($start_date, $end_date);
        $invoices = $invoiceB2c->merge($invoiceB2b);
        $receipts = $this->receiptRepository->getReportReceipt($start_date, $end_date);

        $totalInvoice = 0;
        $totalInvB2b = 0;
        $totalInvB2c = 0;

        foreach ($invoices as $invoice) {
            if (isset($invoice->inv_id)) {
                $totalInvB2c += $invoice->inv_totalprice_idr;
            } else {
                $totalInvB2b += $invoice->invb2b_totpriceidr;
            }
        }

        $totalInvoice = $totalInvB2b + $totalInvB2c;


        return view('pages.report.invoice.index')->with(
            [
                'invoices' => $invoices,
                'totalInvoice' => $totalInvoice,
                'receipts' => $receipts,
            ]
        );
    }
}

<?php

namespace App\Http\Controllers;


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
use App\Models\ClientEvent;
use App\Models\UserClient;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
        ReferralRepositoryInterface $referralRepository
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
    }

    public function event(Request $request)
    {

        $eventId = null;
        if ($request->get('event_id') != null) {
            $eventId = $request->get('event_id');
        }

        if ($request->ajax()) {
            return $this->clientEventRepository->getReportClientEventsDataTables($eventId);
        }

        $events = $this->eventRepository->getAllEvents();

        $choosen_event = $this->eventRepository->getEventById($eventId);
        $clientEvents = $this->clientEventRepository->getReportClientEvents($eventId);
        $allClientEvents = $this->clientEventRepository->getAllClientEvents();
        $clients = $this->clientEventRepository->getReportClientEventsGroupByRoles($eventId);
        $conversionLeads = $this->clientEventRepository->getConversionLead(['eventId' => $eventId]);

        # new get feeder data
        $feeder = $this->schoolRepository->getFeederSchools($eventId);

        $existingMentee = $clients->where('role_name', 'Mentee')->unique('client_id');

        $id_mentee = $this->getIdClient($existingMentee);

        $existingNonMentee = $clients->where('role_name', '!=', 'Mentee')->where('status', 1)->where('main_prog_id', '!=', 1)->whereNotIn('client_id', $id_mentee)->unique('client_id');

        $id_nonMentee = $this->getIdClient($existingNonMentee);

        $undefinedClients = $clients->whereNotIn('client_id', $id_nonMentee)->whereNotIn('client_id', $id_mentee)->unique('client_id');

        $checkClient = $this->checkExistingOrNewClientEvent($undefinedClients, $allClientEvents);

        $id_nonClient = $this->getIdClient($checkClient->where('type', 'ExistNonClient'));

        $existingNonClient = $clients->whereIn('client_id', $id_nonClient)->unique('client_id');

        $id_newClient = $this->getIdClient($checkClient->where('type', 'New'));

        $newClient = $clients->whereIn('client_id', $id_newClient)->unique('client_id');

        return view('pages.report.event-tracking.index')->with(
            [
                // 'clientEvents' => $clientEvents,
                'existingMentee' => $existingMentee,
                'existingNonMentee' => $existingNonMentee,
                'existingNonClient' => $existingNonClient,
                'newClient' => $newClient,
                'events' => $events,
                'conversionLeads' => $conversionLeads,
                'choosen_event' => $choosen_event,
                'feeder' => $feeder,
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
        $referrals_in = $this->referralRepository->getReportNewReferral($start_date, $end_date, 'In');
        $referrals_out = $this->referralRepository->getReportNewReferral($start_date, $end_date, 'Out');

        return view('pages.report.partnership.index')->with(
            [
                'partnerPrograms' => $partnerPrograms,
                'schoolPrograms' => $schoolPrograms,
                'schools' => $schools,
                'schoolVisits' => $schoolVisits,
                'partners' => $partners,
                'universities' => $universities,
                'referrals_in' => $referrals_in,
                'referrals_out' => $referrals_out,
            ]
        );
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
        $totalInvoice = 0;

        // $data_receipts = $receipts->filter(function ($item) {
        //     // Return true if you want this item included in the resultant collection
        //     return $item->status_where == 1 || $item->referral_type == 'Out';
        // });


        $countInvoice = count($invoiceB2b->where('invb2b_pm', 'Full Payment')) + $invoiceB2b->sum('inv_detail_count');
        $countInvoice += count($invoiceB2c->where('inv_paymentmethod', 'Full Payment')) + $invoiceB2c->sum('invoice_detail_count');

        $totalInvoice = $invoiceB2b->sum('invb2b_totpriceidr') + $invoiceB2c->sum('inv_totalprice_idr');

        foreach ($receipts as $receipt) {
            $totalReceipt += (int)filter_var($receipt->receipt_amount_idr, FILTER_SANITIZE_NUMBER_INT);
        }

        return view('pages.report.invoice.index')->with(
            [
                'invoices' => $invoices,
                'countInvoice' => $countInvoice,
                'totalInvoice' => $totalInvoice,
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

        // return $invoiceB2bReport;
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

    protected function getIdClient($data)
    {
        $id_client = array();

        $i = 0;
        foreach ($data as $d) {
            $id_client[$i] = $d->client_id;
            $i++;
        }

        return $id_client;
    }

    protected function checkExistingOrNewClientEvent($data, $allClientEvents)
    {

        $i = 0;

        $dataClient =  new Collection();

        foreach ($data as $data) {

            $child = $allClientEvents->where('child_id', '!=', null)->where('child_id', $data->client_id);
            $student = $allClientEvents->where('child_id', null)->where('client_id', $data->client_id);
            $check = $child->merge($student);
            if (count($check) > 1) {
                $dataClient->push((object)[
                    'type' => 'ExistNonClient',
                    'client_id' => $check->first()->child_id != null ? $check->first()->child_id : $check->first()->client_id,
                ]);
                // $extNonClient = $clients->where('client_id', $check->first()->client_id)->unique('client_id');
            } else {
                // $NewClient = $clients->where('client_id', $check->first()->client_id)->unique('client_id');
                // $dataClient['type'][$i] = 'New';
                // $dataClient['id_client'][$i] = $check->first()->client_id;
                $dataClient->push((object)[
                    'type' => 'New',
                    'client_id' => $check->first()->child_id != null ? $check->first()->child_id : $check->first()->client_id,
                ]);
            }
            $i++;
        }
        return $dataClient;
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

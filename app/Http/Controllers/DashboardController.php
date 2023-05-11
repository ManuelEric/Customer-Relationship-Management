<?php

namespace App\Http\Controllers;

use App\Actions\FetchClientStatus;
use App\Http\Controllers\Module\SalesDashboardController;
use App\Http\Controllers\Module\testController;
use App\Http\Traits\Modules\GetClientStatusTrait;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\FollowupRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SalesTargetRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\PartnerAgreementRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\RefundRepositoryInterface;
use App\Repositories\ClientRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends SalesDashboardController
{

    use GetClientStatusTrait;
    protected ClientRepositoryInterface $clientRepository;
    protected FollowupRepositoryInterface $followupRepository;
    protected CorporateRepositoryInterface $corporateRepository;
    protected SchoolRepositoryInterface $schoolRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected PartnerAgreementRepositoryInterface $partnerAgreementRepository;
    protected AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;
    protected PartnerProgramRepositoryInterface $partnerProgramRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected ReferralRepositoryInterface $referralRepository;
    protected ClientProgramRepositoryInterface $clientProgramRepository;
    protected UserRepositoryInterface $userRepository;
    protected SalesTargetRepositoryInterface $salesTargetRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected ClientEventRepositoryInterface $clientEventRepository;
    protected EventRepositoryInterface $eventRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected RefundRepositoryInterface $refundRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, FollowupRepositoryInterface $followupRepository, CorporateRepositoryInterface $corporateRepository, SchoolRepositoryInterface $schoolRepository, UniversityRepositoryInterface $universityRepository, PartnerAgreementRepositoryInterface $partnerAgreementRepository, AgendaSpeakerRepositoryInterface $agendaSpeakerRepository, PartnerProgramRepositoryInterface $partnerProgramRepository, SchoolProgramRepositoryInterface $schoolProgramRepository, ReferralRepositoryInterface $referralRepository, UserRepositoryInterface $userRepository, ClientProgramRepositoryInterface $clientProgramRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceProgramRepositoryInterface $invoiceProgramRepository, ReceiptRepositoryInterface $receiptRepository, SalesTargetRepositoryInterface $salesTargetRepository, ProgramRepositoryInterface $programRepository, ClientEventRepositoryInterface $clientEventRepository, EventRepositoryInterface $eventRepository, RefundRepositoryInterface $refundRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->followupRepository = $followupRepository;
        $this->corporateRepository = $corporateRepository;
        $this->schoolRepository = $schoolRepository;
        $this->universityRepository = $universityRepository;
        $this->partnerAgreementRepository = $partnerAgreementRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
        $this->partnerProgramRepository = $partnerProgramRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->referralRepository = $referralRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->userRepository = $userRepository;
        $this->salesTargetRepository = $salesTargetRepository;
        $this->programRepository = $programRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->eventRepository = $eventRepository;

        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->receiptRepository = $receiptRepository;
        $this->refundRepository = $refundRepository;
    }

    public function index(Request $request)
    {
        $data = (new SalesDashboardController($this))->get($request);
        $data = array_merge($data, $this->indexPartnership($request));
        $data = array_merge($data, $this->indexFinance($request));

        return view('pages.dashboard.index')->with($data);
    }

    public function indexPartnership($request)
    {
        $date = null;

        $totalPartner = $this->corporateRepository->getAllCorporate()->count();
        $totalSchool = $this->schoolRepository->getAllSchools()->count();
        $totalUniversity = $this->universityRepository->getAllUniversities()->count();
        $totalAgreement = $this->partnerAgreementRepository->getPartnerAgreementByMonthly(date('Y-m'), 'total');
        $newPartner = $this->corporateRepository->getCorporateByMonthly(date('Y-m'), 'total');
        $newSchool = $this->schoolRepository->getSchoolByMonthly(date('Y-m'), 'total');
        $newUniversity = $this->universityRepository->getUniversityByMonthly(date('Y-m'), 'total');

        // Tab Agenda
        $speakers = $this->agendaSpeakerRepository->getAllSpeakerDashboard('all', $date);
        $speakerToday = $this->agendaSpeakerRepository->getAllSpeakerDashboard('byDate', date('Y-m-d'));

        // Tab Partnership
        $partnerPrograms = $this->partnerProgramRepository->getAllPartnerProgramByStatusAndMonth(0, date('Y-m')); # display default partnership program (status pending)

        // Tab Program Comparison
        $startYear = date('Y') - 1;
        $endYear = date('Y');

        $schoolProgramComparison = $this->schoolProgramRepository->getSchoolProgramComparison($startYear, $endYear);
        $partnerProgramComparison = $this->partnerProgramRepository->getPartnerProgramComparison($startYear, $endYear);
        $referralComparison = $this->referralRepository->getReferralComparison($startYear, $endYear);

        $programComparisonMerge = $this->mergeProgramComparison($schoolProgramComparison, $partnerProgramComparison, $referralComparison);

        $programComparisons = $this->mappingProgramComparison($programComparisonMerge);

        # on client event tab
        $cp_filter['qyear'] = 'current';
        $events = [];
        if ($this->eventRepository->getEventsWithParticipants($cp_filter)->count() > 0) {
            $events = $this->eventRepository->getEventsWithParticipants($cp_filter);
            $cp_filter['eventId'] = $events[0]->event_id;
        }

        $conversion_lead_of_event = $this->clientEventRepository->getConversionLead($cp_filter);

        return [
            'totalPartner' => $totalPartner,
            'totalSchool' => $totalSchool,
            'totalUniversity' => $totalUniversity,
            'totalAgreement' => $totalAgreement,
            'newPartner' => $newPartner,
            'newSchool' => $newSchool,
            'newUniversity' => $newUniversity,
            'speakers' => $speakers,
            'speakerToday' => $speakerToday,
            'partnerPrograms' => $partnerPrograms,
            'programComparisons' => $programComparisons,
            # client event tab
            'events' => $events,
            'conversion_lead_of_event' => $conversion_lead_of_event
        ];
    }

    protected function mappingProgramComparison($data)
    {
        return $data->mapToGroups(function ($item, $key) {
            return [
                $item['program_name'] . ' - ' . $item['type'] => [
                    'program_name' => $item['program_name'],
                    'type' => $item['type'],
                    'year' => $item['year'],

                    $item['year'] =>
                    [
                        'participants' => $item['participants'],
                        'total' => $item['total'],
                    ]
                ],
            ];
        });
    }

    protected function mergeProgramComparison($schoolProgram, $partnerProgram, $referral)
    {
        $collection = collect($schoolProgram);
        return $collection->merge($partnerProgram)->merge($referral);
    }

    public function indexFinance()
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
            'outstandingToday' => count($unpaidPayments->where('invoice_duedate', date('Y-m-d'))),
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

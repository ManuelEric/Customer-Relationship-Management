<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
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


    public function __construct(ClientRepositoryInterface $clientRepository, FollowupRepositoryInterface $followupRepository, CorporateRepositoryInterface $corporateRepository, SchoolRepositoryInterface $schoolRepository, UniversityRepositoryInterface $universityRepository, PartnerAgreementRepositoryInterface $partnerAgreementRepository, AgendaSpeakerRepositoryInterface $agendaSpeakerRepository, PartnerProgramRepositoryInterface $partnerProgramRepository, SchoolProgramRepositoryInterface $schoolProgramRepository, ReferralRepositoryInterface $referralRepository, UserRepositoryInterface $userRepository, ClientProgramRepositoryInterface $clientProgramRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository, InvoiceProgramRepositoryInterface $invoiceProgramRepository, ReceiptRepositoryInterface $receiptRepository, SalesTargetRepositoryInterface $salesTargetRepository, ProgramRepositoryInterface $programRepository, ClientEventRepositoryInterface $clientEventRepository, EventRepositoryInterface $eventRepository)
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
    }

    public function index(Request $request)
    {
        $data = $this->indexSales($request);
        $data = array_merge($data, $this->indexPartnership($request));
        $data = array_merge($data, $this->indexFinance($request));

        return view('pages.dashboard.index')->with($data);
    }

    # sales dashboard

    public function indexSales($request)
    {

        # data at the top of dashboard
        $month = date('Y-m');
        $filter = null;
        if ($request->get('month')) {
            $filter = $month = $request->get('month');
        }
        $totalClientByStatus = [
            'prospective' => $this->clientRepository->getCountTotalClientByStatus(0, $filter), # prospective
            'potential' => $this->clientRepository->getCountTotalClientByStatus(1, $filter), # potential
            'current' => $this->clientRepository->getCountTotalClientByStatus(2, $filter), # current
            'completed' => $this->clientRepository->getCountTotalClientByStatus(3, $filter), # current
            'mentee' => $this->clientRepository->getAllClientByRole('mentee', $filter)->count(),
            'alumni' => $this->clientRepository->getAllClientByRole('alumni', $filter)->count(),
            'parent' => $this->clientRepository->getAllClientByRole('parent', $filter)->count(),
            'teacher_counselor' => $this->clientRepository->getAllClientByRole('Teacher/Counselor', $filter)->count()
        ];
        $followUpReminder = $this->followupRepository->getAllFollowupWithin(7, $filter);
        $menteesBirthday = $this->clientRepository->getMenteesBirthdayMonthly($month);

        # data at the body of dashboard
        $employees = $this->userRepository->getAllUsersByRole('employee');

        # on client program tab
        $cp_filter['qdate'] = $request->get('qdate') ?? date('Y-m');
        if ($request->get('cp-month')) { # format Y-m
            $cp_filter['qdate'] = $request->get('cp-month');
        }

        $dateDetails = [
            'startDate' => $cp_filter['qdate'] . '-01',
            'endDate' => $cp_filter['qdate'] . '-31'
        ];

        # if (admin)
        # then null
        # elif not admin
        # their uuid

        if ($request->get('quser')) {
            $cp_filter['quuid'] = $request->get('quser');
        }

        # client program status
        $totalAllClientProgramByStatus = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => null] + $cp_filter);

        # admissions mentoring
        $admissionsMentoring = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Admissions Mentoring'] + $cp_filter);

        # initial consultation
        $initialConsultation = $this->clientProgramRepository->getInitialConsultationInformation($cp_filter);
        $totalInitialConsultation = array_sum($initialConsultation);
        $successProgram = $initialConsultation[2];
        $successProgram;

        $initialAssessmentMaking = $this->clientProgramRepository->getInitialMaking($dateDetails, $cp_filter);
        $conversionTimeProgress = $this->clientProgramRepository->getConversionTimeProgress($dateDetails, $cp_filter);
        $successPercentage = $successProgram == 0 ? 0 : ($successProgram / $totalInitialConsultation) * 100;
        $allSuccessProgramByMonth = $this->clientProgramRepository->getSuccessProgramByMonth($cp_filter);
        $totalRevenueAdmMentoringByProgramAndMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Admissions Mentoring'] + $cp_filter);
        $academicTestPrep = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Academic & Test Preparation'] + $cp_filter);
        $totalRevenueAcadTestPrepByMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Academic & Test Preparation'] + $cp_filter);
        $careerExploration = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Career Exploration'] + $cp_filter);
        $totalRevenueCareerExplorationByMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Career Exploration'] + $cp_filter);

        $leadSource = $this->clientProgramRepository->getLeadSource($dateDetails, $cp_filter);
        $conversionLeads = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter);

        $admissionMentoringConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter + ['prog' => 'Admissions Mentoring']);
        $academicTestPrepConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter + ['prog' => 'Academic & Test Preparation']);
        $careerExplorationConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter + ['prog' => 'Career Exploration']);

        # on sales target tab
        $programId = null; # means all programs
        $salesTarget = $this->salesTargetRepository->getMonthlySalesTarget($programId, $cp_filter);
        $salesActual = $this->salesTargetRepository->getMonthlySalesActual($programId, $cp_filter);

        $salesDetail = $this->salesTargetRepository->getSalesDetail($programId, $cp_filter);

        # on program comparison tab
        $allPrograms = $this->programRepository->getAllPrograms()->groupBy('main_prog.prog_name');
        $cp_filter['queryParams_year1'] = date('Y') - 1;
        $cp_filter['queryParams_year2'] = (int) date('Y');

        $comparisons = $this->clientProgramRepository->getComparisonBetweenYears($cp_filter);

        # on client event tab
        $cp_filter['qyear'] = 'current';
        $events = [];
        if ($this->eventRepository->getEventsWithParticipants($cp_filter)->count() > 0) {
            $events = $this->eventRepository->getEventsWithParticipants($cp_filter);
            $cp_filter['eventId'] = $events[0]->event_id;
        }


        $conversion_lead_of_event = $this->clientEventRepository->getConversionLead($cp_filter);

        return [
            'totalClientInformation' => $totalClientByStatus,
            'followUpReminder' => $followUpReminder,
            'menteesBirthday' => $menteesBirthday,
            'filter_bymonth' => $filter,

            # client program tab
            'employees' => $employees,
            'clientProgramGroupByStatus' => $totalAllClientProgramByStatus,
            'admissionsMentoring' => $admissionsMentoring,
            'initialConsultation' => $initialConsultation,
            'totalInitialConsultation' => $totalInitialConsultation,
            'successProgram' => $successProgram,
            'initialAssessmentMaking' => $initialAssessmentMaking,
            'conversionTimeProgress' => $conversionTimeProgress,
            'successPercentage' => $successPercentage,
            'allSuccessProgramByMonth' => $allSuccessProgramByMonth,
            'totalRevenueAdmissionMentoring' => $totalRevenueAdmMentoringByProgramAndMonth,
            'academicTestPrep' => $academicTestPrep,
            'totalRevenueAcadTestPrepByMonth' => $totalRevenueAcadTestPrepByMonth,
            'careerExploration' => $careerExploration,
            'totalRevenueCareerExplorationByMonth' => $totalRevenueCareerExplorationByMonth,
            'leadSource' => $leadSource,
            'conversionLeads' => $conversionLeads,
            'adminssionMentoringConvLead' => $admissionMentoringConvLead,
            'academicTestPrepConvLead' => $academicTestPrepConvLead,
            'careerExplorationConvLead' => $careerExplorationConvLead,

            # sales target tab
            'salesTarget' => $salesTarget,
            'salesActual' => $salesActual,
            'salesDetail' => $salesDetail,

            # program comparison
            'allPrograms' => $allPrograms,
            'comparisons' => $comparisons,

            # client event tab
            'events' => $events,
            'conversion_lead_of_event' => $conversion_lead_of_event
        ];
    }

    public function indexPartnership($request)
    {
        $date = null;

        $totalPartner = $this->corporateRepository->getAllCorporate()->count();
        $totalSchool = $this->schoolRepository->getAllSchools()->count();
        $totalUniversity = $this->universityRepository->getAllUniversities()->count();
        $totalAgreement = $this->partnerAgreementRepository->getCountTotalPartnerAgreementByMonthly(date('Y-m'));
        $newPartner = $this->corporateRepository->getCountTotalCorporateByMonthly(date('Y-m'));
        $newSchool = $this->schoolRepository->getCountTotalSchoolByMonthly(date('Y-m'));
        $newUniversity = $this->universityRepository->getCountTotalUniversityByMonthly(date('Y-m'));

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

        $totalRefundRequestB2b = $this->invoiceB2bRepository->getTotalRefundRequest(date('Y-m'));
        $totalRefundRequestB2c = $this->invoiceProgramRepository->getTotalRefundRequest(date('Y-m'));

        $totalReceipt = $this->receiptRepository->getTotalReceipt(date('Y-m'));

        $totalInvoiceNeeded = collect($totalInvoiceNeededB2b)->merge($totalInvoiceNeededB2c)->sum('count_invoice_needed');
        $totalInvoice = collect($totalInvoiceB2b)->merge($totalInvoiceB2c);

        $totalRefundRequest = collect($totalRefundRequestB2b)->merge($totalRefundRequestB2c)->sum('count_refund_request');

        $paidPaymentB2b = $this->invoiceB2bRepository->getInvoiceOutstandingPayment(date('Y-m'), 'paid', null, null);
        $paidPaymentB2c = $this->invoiceProgramRepository->getInvoiceOutstandingPayment(date('Y-m'), 'paid');

        $paidPayments = collect($paidPaymentB2b)->merge($paidPaymentB2c);

        $unpaidPaymentB2b = $this->invoiceB2bRepository->getInvoiceOutstandingPayment(date('Y-m'), 'unpaid', null, null);
        $unpaidPaymentB2c = $this->invoiceProgramRepository->getInvoiceOutstandingPayment(date('Y-m'), 'unpaid');

        $unpaidPayments = collect($unpaidPaymentB2b)->merge($unpaidPaymentB2c);

        $revenueB2b = $this->invoiceB2bRepository->getRevenueByYear(date('Y'));
        $revenueB2c = $this->invoiceProgramRepository->getRevenueByYear(date('Y'));

        $revenue = collect($revenueB2b)->merge($revenueB2c)->groupBy('month')->map(
            function ($row) {
                return $row->sum('total');
            }
        );

        return [
            'totalInvoiceNeeded' => $totalInvoiceNeeded,
            'totalInvoice' => $totalInvoice,
            'totalReceipt' => $totalReceipt,
            'totalRefundRequest' => $totalRefundRequest,
            'paidPayments' => $paidPayments,
            'unpaidPayments' => $unpaidPayments,
            'revenue' => $revenue,
        ];
    }
}

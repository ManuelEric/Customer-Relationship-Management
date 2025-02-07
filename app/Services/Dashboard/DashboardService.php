<?php

namespace App\Services\Dashboard;

use App\Http\Traits\Modules\GetClientStatusTrait;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\FollowupRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\LeadTargetRepositoryInterface;
use App\Interfaces\PartnerAgreementRepositoryInterface;
use App\Interfaces\PartnerProgramRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReceiptRepositoryInterface;
use App\Interfaces\ReferralRepositoryInterface;
use App\Interfaces\RefundRepositoryInterface;
use App\Interfaces\SalesTargetRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    // use GetClientStatusTrait;

    protected UserRepositoryInterface $userRepository;
    protected ClientProgramRepositoryInterface $clientProgramRepository;
    protected SalesTargetRepositoryInterface $salesTargetRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected EventRepositoryInterface $eventRepository;
    protected ClientEventRepositoryInterface $clientEventRepository;
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
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;
    protected InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    protected ReceiptRepositoryInterface $receiptRepository;
    protected RefundRepositoryInterface $refundRepository;
    protected LeadRepositoryInterface $leadRepository;
    protected LeadTargetRepositoryInterface $leadTargetRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ClientProgramRepositoryInterface $clientProgramRepository,
        SalesTargetRepositoryInterface $salesTargetRepository,
        ProgramRepositoryInterface $programRepository,
        EventRepositoryInterface $eventRepository,
        ClientEventRepositoryInterface $clientEventRepository,
        ClientRepositoryInterface $clientRepository,
        FollowupRepositoryInterface $followupRepository,
        CorporateRepositoryInterface $corporateRepository,
        SchoolRepositoryInterface $schoolRepository,
        UniversityRepositoryInterface $universityRepository,
        PartnerAgreementRepositoryInterface $partnerAgreementRepository,
        AgendaSpeakerRepositoryInterface $agendaSpeakerRepository,
        PartnerProgramRepositoryInterface $partnerProgramRepository,
        SchoolProgramRepositoryInterface $schoolProgramRepository,
        ReferralRepositoryInterface $referralRepository,
        InvoiceB2bRepositoryInterface $invoiceB2bRepository,
        InvoiceProgramRepositoryInterface $invoiceProgramRepository,
        ReceiptRepositoryInterface $receiptRepository,
        RefundRepositoryInterface $refundRepository,
        LeadRepositoryInterface $leadRepository,
        LeadTargetRepositoryInterface $leadTargetRepository
        )
    {
        $this->userRepository = $userRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->salesTargetRepository = $salesTargetRepository;
        $this->programRepository = $programRepository;
        $this->eventRepository = $eventRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->corporateRepository = $corporateRepository;
        $this->schoolRepository = $schoolRepository;
        $this->universityRepository = $universityRepository;
        $this->partnerAgreementRepository = $partnerAgreementRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
        $this->partnerProgramRepository = $partnerProgramRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->referralRepository = $referralRepository;
        $this->invoiceB2bRepository = $invoiceB2bRepository;
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->receiptRepository = $receiptRepository;
        $this->refundRepository = $refundRepository;
        $this->leadRepository = $leadRepository;
        $this->leadTargetRepository = $leadTargetRepository;

        /**
         * clientStatusTrait requirements
         */
        $this->clientRepository = $clientRepository;
        $this->followupRepository = $followupRepository;
    }
    

    public function snSalesDashboard(Array $filter)
    {
        # INITIALIZE PARAMETERS START
        $date_details = [
            'start' => $filter['start'],
            'end' => $filter['end'],
        ];
        $program_id = $filter['program_id']; # means all programs
        $events = [];
        # INITIALIZE PARAMETERS END

        # fetching client status data
        $response_of_client_status = $this->clientStatus(Carbon::now()->format('Y-m'));
        return $response_of_client_status;

        # fetching all employee data
        $employees = $this->userRepository->rnGetAllUsersByDepartmentAndRole('employee', 'Client Management');
        return $employees;

        # fetching chart data by no program (all)
        $total_all_client_program_by_status = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => null] + $filter);

        # fetching chart data by admission mentoring
        $admissions_mentoring = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Admissions Mentoring'] + $filter);

        # fetching chart data by initial consultation
        $initial_consultation = $this->clientProgramRepository->getInitialConsultationInformation($filter);

        # sum total initial consultation by summing the data
        $total_initial_consultation = array_sum($initial_consultation);
        
        # fetching only already from initial consultation chart data
        $already = $initial_consultation[1];

        # fetching only already from initial consultation chart data
        $already = $initial_consultation[1];

        # fetching only success program from initial consultation chart data
        $success_program = $initial_consultation[2];

        # get initial assessment making (average days)
        $initial_assessment_making = $this->clientProgramRepository->getInitialMaking($date_details, $filter);

        # get conversion time progress (average days)
        $conversion_time_progress = $this->clientProgramRepository->getConversionTimeProgress($date_details, $filter);

        # get initial consultation success percentage 
        $success_percentage = $success_program == 0 ? 0 : ($success_program / $total_initial_consultation) * 100;

        # get total revenue of admission mentoring program
        $total_revenue_adm_mentoring_by_program_and_month = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Admissions Mentoring'] + $filter);

        # fetching successful programs
        $all_success_program_by_month = $this->clientProgramRepository->getSuccessProgramByMonth($filter);

        # fetching chart data by academic & test preparation program
        $academic_test_prep = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Academic & Test Preparation'] + $filter);

        # get total revenue by academic & test preparation program
        $total_revenue_acad_test_prep_by_month = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Academic & Test Preparation'] + $filter);

        # fetching chart data by career exploration program
        $career_exploration = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Career Exploration'] + $filter);

        # get total revenue by career exploration program
        $total_revenue_career_exploration_by_month = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Career Exploration'] + $filter);

        # fetching chart data of lead source as a whole (admission mentoring, academic & test preparation, career exploration)
        $lead_source = $this->clientProgramRepository->rnGetLeadSource($date_details, $filter);

        # fetching chart data of conversion leads as a whole (admission mentoring, academic & test preparation, career exploration)
        $conversion_leads = $this->clientProgramRepository->rnGetConversionLead($date_details, $filter);

        # fetching chart data of conversion lead by admission mentoring program
        $admission_mentoring_conv_lead = $this->clientProgramRepository->rnGetConversionLead($date_details, $filter + ['prog' => 'Admissions Mentoring']);

        # fetching chart data of conversion lead by academic & test preparation program
        $academic_test_prep_conv_lead = $this->clientProgramRepository->rnGetConversionLead($date_details, $filter + ['prog' => 'Academic & Test Preparation']);

        # fetching chart data of conversion lead by career exploration program
        $career_exploration_conv_lead = $this->clientProgramRepository->rnGetConversionLead($date_details, $filter + ['prog' => 'Career Exploration']);

        # fetching the target sales
        $sales_target = $this->salesTargetRepository->getMonthlySalesTarget($program_id, $filter);

        # fetching the actual sales
        $sales_actual = $this->salesTargetRepository->getMonthlySalesActual($program_id, $filter);

        # fetching the detail of target sales & actual sales by month
        $sales_detail = $this->salesTargetRepository->getSalesDetail($program_id, $filter);

        # fetching all programs for a list that can be choose by user
        $all_programs = $this->programRepository->getAllPrograms()->groupBy('main_prog.prog_name');

        # fetching all programs including revenue per program yearly
        $comparisons = $this->clientProgramRepository->getComparisonBetweenYears($filter);


        if ($this->eventRepository->getEventsWithParticipants($filter)->count() > 0) {
            $events = $this->eventRepository->getEventsWithParticipants($filter);
            $filter['eventId'] = $events[0]->event_id;
        }

        # fetching conversion lead by client event
        $conversion_lead_of_event = $this->clientEventRepository->getConversionLead($filter);

        return $response_of_client_status + [

            # client program tab
            'employees' => $employees,
            'client_program_group_by_status' => $total_all_client_program_by_status,
            'admissions_mentoring' => $admissions_mentoring,
            'initial_consultation' => $initial_consultation,
            'total_initial_consultation' => $total_initial_consultation,
            'already' => $already,
            'success_program' => $success_program,
            'initial_assessment_making' => $initial_assessment_making,
            'conversion_time_progress' => $conversion_time_progress,
            'success_percentage' => $success_percentage,
            'all_success_program_by_month' => $all_success_program_by_month,
            'total_revenue_adm_mentoring_by_program_and_month' => $total_revenue_adm_mentoring_by_program_and_month,
            'academic_test_prep' => $academic_test_prep,
            'total_revenue_acad_test_prep_by_month' => $total_revenue_acad_test_prep_by_month,
            'career_exploration' => $career_exploration,
            'total_revenue_career_exploration_by_month' => $total_revenue_career_exploration_by_month,
            'lead_source' => $lead_source,
            'conversion_leads' => $conversion_leads,
            'admission_mentoring_conv_lead' => $admission_mentoring_conv_lead,
            'academic_test_prep_conv_lead' => $academic_test_prep_conv_lead,
            'career_exploration_conv_lead' => $career_exploration_conv_lead,

            # sales target tab
            'sales_target' => $sales_target,
            'sales_actual' => $sales_actual,
            'sales_detail' => $sales_detail,

            # program comparison
            'all_programs' => $all_programs,
            'comparisons' => $comparisons,

            # client event tab
            'events' => $events,
            'conversion_lead_of_event' => $conversion_lead_of_event,
        ];
    }

    public function clientStatus($month)
    {
        $asDatatables = $groupBy = false;
        $total_client_by_category_all = $this->clientRepository->countClientByCategoryAllCategory();
        $total_client_by_category_monthly = $this->clientRepository->countClientByCategoryAllCategory($month);

        $total_newLeads = $total_client_by_category_all->where('category', 'new-lead')->first()->client_count ?? 0;
        $monthly_new_newLeads = $total_client_by_category_monthly->where('category', 'new-lead')->first()->client_count ?? 0;

        $total_potential_client = $total_client_by_category_all->where('category', 'potential')->first()->client_count ?? 0;
        $monthly_new_potential_client = $total_client_by_category_monthly->where('category', 'potential')->first()->client_count ?? 0;

        $total_existingMentees = $total_client_by_category_all->where('category', 'mentee')->first()->client_count ?? 0;
        $monthly_new_existingMentees = $total_client_by_category_monthly->where('category', 'mentee')->first()->client_count ?? 0;

        $total_existingNonMentees = $total_client_by_category_all->where('category', 'non-mentee')->first()->client_count ?? 0;
        $monthly_new_existingNonMentees = $total_client_by_category_monthly->where('category', 'mentee')->first()->client_count ?? 0;

        $total_alumniMentees = $total_client_by_category_all->where('category', 'alumni-mentee')->first()->client_count ?? 0;
        $monthly_new_alumniMentees = $total_client_by_category_monthly->where('category', 'alumni-mentee')->first()->client_count ?? 0;

        $total_alumniNonMentees = $total_client_by_category_all->where('category', 'alumni-non-mentee')->first()->client_count ?? 0;
        $monthly_new_alumniNonMentees = $total_client_by_category_monthly->where('category', 'alumni-non-mentee')->first()->client_count ?? 0;


        // $total_newLeads = $this->clientRepository->countClientByCategory('new-lead');
        // $monthly_new_newLeads = $this->clientRepository->countClientByCategory('new-lead', $month);

        // $total_potential_client = $this->clientRepository->countClientByCategory('potential');
        // $monthly_new_potential_client = $this->clientRepository->countClientByCategory('potential', $month);

        // $total_existingMentees = $this->clientRepository->countClientByCategory('mentee');
        // $monthly_new_existingMentees = $this->clientRepository->countClientByCategory('mentee', $month);

        // $total_existingNonMentees = $this->clientRepository->countClientByCategory('non-mentee');
        // $monthly_new_existingNonMentees = $this->clientRepository->countClientByCategory('non-mentee', $month);

        // $total_alumniMentees = $this->clientRepository->countClientByCategory('alumni-mentee');
        // $monthly_new_alumniMentees = $this->clientRepository->countClientByCategory('alumni-mentee', $month);

        // $total_alumniNonMentees = $this->clientRepository->countClientByCategory('alumni-non-mentee');
        // $monthly_new_alumniNonMentees = $this->clientRepository->countClientByCategory('alumni-non-mentee', $month);

        // $total_parent = $this->clientRepository->countClientByRole('Parent');
        // $monthly_new_parent = $this->clientRepository->countClientByRole('Parent', $month);

        // $total_teacher = $this->clientRepository->countClientByRole('Teacher/Counselor');
        // $monthly_new_teacher = $this->clientRepository->countClientByRole('Teacher/Counselor', $month);
        $total_parent = 0;
        $monthly_new_parent = 0;

        $total_teacher = 0;
        $monthly_new_teacher = 0;

        # data at the top of dashboard
        $response['totalClientInformation'] = [
            'newLeads' => [
                'old' => $total_newLeads - $monthly_new_newLeads,
                'new' => $monthly_new_newLeads,
                'percentage' => $this->calculatePercentage($total_newLeads, $monthly_new_newLeads)
            ], # prospective
            'potential' => [
                'old' => $total_potential_client - $monthly_new_potential_client,
                'new' => $monthly_new_potential_client,
                'percentage' => $this->calculatePercentage($total_potential_client, $monthly_new_potential_client)
            ], # potential
            'existingMentees' => [
                'old' => $total_existingMentees - $monthly_new_existingMentees,
                'new' => $monthly_new_existingMentees,
                'percentage' => $this->calculatePercentage($total_existingMentees, $monthly_new_existingMentees)
            ], # current
            'existingNonMentees' => [
                'old' => $total_existingNonMentees - $monthly_new_existingNonMentees,
                'new' => $monthly_new_existingNonMentees,
                'percentage' => $this->calculatePercentage($total_existingNonMentees, $monthly_new_existingNonMentees)
            ], # current
            'alumniMentees' => [
                'old' => $total_alumniMentees - $monthly_new_alumniMentees,
                'new' => $monthly_new_alumniMentees,
                'percentage' => $this->calculatePercentage($total_alumniMentees, $monthly_new_alumniMentees)
            ],
            'alumniNonMentees' => [
                'old' => $total_alumniNonMentees - $monthly_new_alumniNonMentees,
                'new' => $monthly_new_alumniNonMentees,
                'percentage' => $this->calculatePercentage($total_alumniNonMentees, $monthly_new_alumniNonMentees)
            ],
            'parent' => [
                'old' => $total_parent - $monthly_new_parent,
                'new' => $monthly_new_parent,
                'percentage' => $this->calculatePercentage($total_parent, $monthly_new_parent)
            ],
            'teacher_counselor' => [
                'old' => $total_teacher - $monthly_new_teacher,
                'new' => $monthly_new_teacher,
                'percentage' => $this->calculatePercentage($total_teacher, $monthly_new_teacher)
            ],
            'raw' => [
                'student' => $this->clientRepository->countClientByCategory('raw'),
                'parent' => $this->clientRepository->countClientByRole('Parent', null, true),
                'teacher' => $this->clientRepository->countClientByRole('Teacher/Counselor', null, true),
            ],
            'inactive' => [
                'student' => $this->clientRepository->getInactiveStudent(false)->count(),
                'parent' => $this->clientRepository->getInactiveParent(false)->count(),
                'teacher' => $this->clientRepository->getInactiveTeacher(false)->count(),
            ]
        ];
        $response['followUpReminder'] = $this->followupRepository->getAllFollowupWithin(7);
        $response['menteesBirthday'] = $this->clientRepository->getMenteesBirthdayMonthly($month);

        return with($response);
    }

    private function calculatePercentage($total_data, $monthly_data)
    {
        if ($total_data == 0)
            return "0,00";

        if (abs($total_data - $monthly_data) == 0)
            return number_format($total_data * 100, 2, ',', '.');

        return number_format(($monthly_data / abs($total_data - $monthly_data)) * 100, 2, ',', '.');
    }

    private function calculatePercentageLead($actual, $target)
    {
        if ($target == 0)
            return 0;

        return $actual/$target*100;
    }

    public function snPartnershipDashboard()
    {
        $date = null;

        $total_partner = $this->corporateRepository->getAllCorporate()->count();
        $total_school = $this->schoolRepository->getAllSchools()->count();
        $total_university = $this->universityRepository->getAllUniversities()->count();
        $total_agreement = $this->partnerAgreementRepository->getPartnerAgreementByMonthly(date('Y-m'), 'all');
        $new_partner = $this->corporateRepository->getCorporateByMonthly(date('Y-m'), 'monthly');
        $new_school = $this->schoolRepository->getSchoolByMonthly(date('Y-m'), 'monthly');
        $new_university = $this->universityRepository->getUniversityByMonthly(date('Y-m'), 'monthly');

        // Tab Agenda
        $speakers = $this->agendaSpeakerRepository->getAllSpeakerDashboard('all', $date);
        $speaker_today = $this->agendaSpeakerRepository->getAllSpeakerDashboard('byDate', date('Y-m-d'));

        // Tab Partnership
        $partner_programs = $this->partnerProgramRepository->getAllPartnerProgramByStatusAndMonth(0, date('Y-m')); # display default partnership program (status pending)

        // Tab Program Comparison
        $start_year = date('Y') - 1;
        $end_year = date('Y');
        $school_program_comparison = $this->schoolProgramRepository->getSchoolProgramComparison($start_year, $end_year);
        $partner_program_comparison = $this->partnerProgramRepository->getPartnerProgramComparison($start_year, $end_year);
        $referral_comparison = $this->referralRepository->getReferralComparison($start_year, $end_year);
        $program_comparison_merge = $this->fnPartnershipProgramComparisonMerger($school_program_comparison, $partner_program_comparison, $referral_comparison);
        $program_comparisons = $this->fnPartnershipProgramComparisonMapping($program_comparison_merge);

        # on client event tab
        $cp_filter['qyear'] = 'current';
        $events = [];
        if ($this->eventRepository->getEventsWithParticipants($cp_filter)->count() > 0) {
            $events = $this->eventRepository->getEventsWithParticipants($cp_filter);
            $cp_filter['eventId'] = $events[0]->event_id;
        }

        $conversion_lead_of_event = $this->clientEventRepository->getConversionLead($cp_filter);
        $uncomplete_schools = $this->schoolRepository->getUncompeteSchools();
        return [
            'totalPartner' => $total_partner,
            'totalSchool' => $total_school,
            'totalUniversity' => $total_university,
            'totalAgreement' => $total_agreement,
            'newPartner' => $new_partner,
            'newSchool' => $new_school,
            'newUniversity' => $new_university,
            'speakers' => $speakers,
            'speakerToday' => $speaker_today,
            'partnerPrograms' => $partner_programs,
            'programComparisons' => $program_comparisons,
            # client event tab
            'events' => $events,
            'conversion_lead_of_event' => $conversion_lead_of_event,
            'totalUncompleteSchool' => $uncomplete_schools->count()
        ];
    }

    protected function fnPartnershipProgramComparisonMerger($schoolProgram, $partnerProgram, $referral)
    {
        $collection = collect($schoolProgram);
        return $collection->merge($partnerProgram)->merge($referral);
    }

    protected function fnPartnershipProgramComparisonMapping($data)
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

    public function snFinanceDashboard(): array
    {
        $total_invoice_needed_from_b2b = $this->invoiceB2bRepository->getTotalInvoiceNeeded(date('Y-m'));
        $total_invoice_needed_from_b2c = $this->invoiceProgramRepository->getTotalInvoiceNeeded(date('Y-m'));

        $total_invoice_b2b = $this->invoiceB2bRepository->getTotalInvoice(date('Y-m'));
        $total_invoice_b2c = $this->invoiceProgramRepository->getTotalInvoice(date('Y-m'));

        // $totalRefundRequestB2b = $this->invoiceB2bRepository->getTotalRefundRequest(date('Y-m'));
        // $totalRefundRequestB2c = $this->invoiceProgramRepository->getTotalRefundRequest(date('Y-m'));

        $total_receipt = $this->receiptRepository->getTotalReceipt(date('Y-m'));

        $total_invoice_needed = collect($total_invoice_needed_from_b2b)->merge($total_invoice_needed_from_b2c);

        $total_refund_request = $this->refundRepository->getTotalRefundRequest(date('Y-m'));

        $paid_payment_b2b = $this->invoiceB2bRepository->getInvoiceOutstandingPayment(date('Y-m'), 'paid', null, null);
        $paid_payment_b2c = $this->invoiceProgramRepository->getInvoiceOutstandingPayment(date('Y-m'), 'paid');

        $paid_payments = collect($paid_payment_b2b)->merge($paid_payment_b2c);

        $unpaid_payment_b2b = $this->invoiceB2bRepository->getInvoiceOutstandingPayment(date('Y-m'), 'unpaid', null, null);
        $unpaid_payment_b2c = $this->invoiceProgramRepository->getInvoiceOutstandingPayment(date('Y-m'), 'unpaid');

        $unpaid_payments = collect($unpaid_payment_b2b)->merge($unpaid_payment_b2c);

        $total_outstanding = $unpaid_payments->count();

        $revenue_b2b = $this->invoiceB2bRepository->getRevenueByYear(date('Y'));
        $revenue_b2c = $this->invoiceProgramRepository->getRevenueByYear(date('Y'));

        $revenue = collect($revenue_b2b)->merge($revenue_b2c)->groupBy('month')->map(
            function ($row) {
                return $row->sum('total');
            }
        );

        $total_invoice[0] = [
            'count_invoice' => count($total_invoice_b2b) + count($total_invoice_b2b),
            'total' => $total_invoice_b2b->where('invb2b_pm', 'Full Payment')->sum('invb2b_totpriceidr') + $total_invoice_b2b->where('invb2b_pm', 'Installment')->sum('invdtl_amountidr')
        ];

        $total_invoice[1] = [
            'count_invoice' => count($total_invoice_b2c),
            'total' => $total_invoice_b2c->where('inv_paymentmethod', 'Full Payment')->sum('inv_totalprice_idr') + $total_invoice_b2c->where('inv_paymentmethod', 'Installment')->sum('invdtl_amountidr')
        ];

        return [
            'invoiceNeededToday' => count($total_invoice_needed->where('success_date', date('Y-m-d'))),
            'outstandingToday' => count($unpaid_payments->where('invoice_duedate', date('Y-m-d', strtotime("-3 days")))),
            'refundRequestToday' => count($total_refund_request->where('refund_date', date('Y-m-d'))),
            'totalInvoiceNeeded' => count($total_invoice_needed),
            'totalInvoice' => $total_invoice,
            'totalReceipt' => $total_receipt,
            'totalRefundRequest' => count($total_refund_request),
            'paidPayments' => $paid_payments,
            'unpaidPayments' => $unpaid_payments,
            'totalOutstanding' => $total_outstanding,
            'revenue' => $revenue,
        ];
    }

    public function snDigitalDashboard()
    {
        $fullDay = Carbon::now()->daysInMonth;
        $midOfMonth = floor($fullDay / 2);
        // $alarm = new Collection();

        $today = Carbon::now()->format('Y-m-d');
        $currMonth = date('m');
        
        # List Lead Source 
        $leads = $this->leadRepository->getAllLead();
        
        $lead_data_from_digital = $this->leadTargetRepository->getLeadDigital($today, $prog_id ?? null);
        // $dataLeadDigtalSource = $this->leadTargetRepository->getLeadDigital($today, false, $prog_id ?? null);
        // $dataConversionLead = $this->leadTargetRepository->getLeadDigital($today, true, $prog_id ?? null);

        // $mergeLeadSourceAndConversionLead = $dataConversionLead->merge($dataLeadDigtalSource);

        $programs_created_by_digital_team = $this->programRepository->getAllPrograms();

        $response = [
            'leadsDigital' => $this->fnDashboardMappingDigitalDataLead($leads, $lead_data_from_digital, 'Lead Source'),
            'leadsAllDepart' => $this->fnDashboardMappingDigitalDataLead($leads, $lead_data_from_digital, 'Conversion Lead'),
            'dataLead' => $lead_data_from_digital,
            'programsDigital' => $programs_created_by_digital_team,

        ];

        return $response;
    }

    private function fnDashboardMappingDigitalDataLead($leads, $dataLead, $type)
    {
        $data = new Collection();
        foreach ($leads as $lead) {
            if($type == 'Lead Source'){
                $count = $dataLead->where('client.lead_id', $lead->lead_id)->count();
            }else if($type == 'Conversion Lead'){
                $count = $dataLead->where('lead_id', $lead->lead_id)->count();
            }
            
            if($count > 0){
                $data->push([
                    'lead_id' => $lead->lead_id,
                    'lead_name' => $lead->main_lead . ($lead->sub_lead  != null ? ' - ' . $lead->sub_lead : ''),
                    'count' => $count,

                ]);
            }
        }

        return $data;
    }
}
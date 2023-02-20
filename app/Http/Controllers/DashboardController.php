<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\FollowupRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SalesTargetRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected ClientRepositoryInterface $clientRepository;
    protected FollowupRepositoryInterface $followupRepository;
    protected ClientProgramRepositoryInterface $clientProgramRepository;
    protected UserRepositoryInterface $userRepository;
    protected SalesTargetRepositoryInterface $salesTargetRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected ClientEventRepositoryInterface $clientEventRepository;
    protected EventRepositoryInterface $eventRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, FollowupRepositoryInterface $followupRepository, ClientProgramRepositoryInterface $clientProgramRepository, UserRepositoryInterface $userRepository, SalesTargetRepositoryInterface $salesTargetRepository, ProgramRepositoryInterface $programRepository, ClientEventRepositoryInterface $clientEventRepository, EventRepositoryInterface $eventRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->followupRepository = $followupRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->userRepository = $userRepository;
        $this->salesTargetRepository = $salesTargetRepository;
        $this->programRepository = $programRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->eventRepository = $eventRepository;

    }

    public function index(Request $request)
    {   
        return $this->indexSales($request);
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
                    'startDate' => $cp_filter['qdate'].'-01', 
                    'endDate' => $cp_filter['qdate'].'-31'
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
                $successProgram = $admissionsMentoring[2];

                $initialAssessmentMaking = $this->clientProgramRepository->getInitialMaking($dateDetails, $cp_filter);
                $conversionTimeProgress = $this->clientProgramRepository->getConversionTimeProgress($dateDetails, $cp_filter);
                $successPercentage = $successProgram == 0 ? 0 : ($successProgram/$totalInitialConsultation) * 100;
                $allSuccessProgramByMonth = $this->clientProgramRepository->getSuccessProgramByMonth($cp_filter);
                $totalRevenueAdmMentoringByProgramAndMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Admissions Mentoring'] + $cp_filter);
                $academicTestPrep = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Academic & Test Preparation'] + $cp_filter);
                $totalRevenueAcadTestPrepByMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Academic & Test Preparation'] + $cp_filter);
                $careerExploration = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Career Exploration'] + $cp_filter);
                $totalRevenueCareerExplorationByMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Career Exploration'] + $cp_filter);

                $leadSource = $this->clientProgramRepository->getLeadSource($dateDetails, $cp_filter);
                $conversionLeads = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter);

                $adminssionMentoringConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter + ['prog' => 'Admissions Mentoring']);
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

        return view('pages.dashboard.index')->with(
            [
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
                'adminssionMentoringConvLead' => $adminssionMentoringConvLead,
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
            ]
        );
    }

}

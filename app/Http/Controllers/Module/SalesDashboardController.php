<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;
use App\Http\Traits\Modules\GetClientStatusTrait;
use App\Interfaces\ClientRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesDashboardController extends Controller
{
    use GetClientStatusTrait;

    public function __construct($repositories)
    {
        $this->clientRepository = $repositories->clientRepository;
        $this->followupRepository = $repositories->followupRepository;
        $this->userRepository = $repositories->userRepository;
        $this->clientProgramRepository = $repositories->clientProgramRepository;
        $this->salesTargetRepository = $repositories->salesTargetRepository;
        $this->programRepository = $repositories->programRepository;
        $this->eventRepository = $repositories->eventRepository;
        $this->clientEventRepository = $repositories->clientEventRepository;
        $this->clientLeadTrackingRepository = $repositories->clientLeadTrackingRepository;
        $this->targetTrackingRepository = $repositories->targetTrackingRepository;
        $this->targetSignalRepository = $repositories->targetSignalRepository;
        $this->invoiceB2bRepository = $repositories->invoiceB2bRepository;
        $this->invoiceProgramRepository = $repositories->invoiceProgramRepository;
        $this->invoicesRepository = $repositories->invoicesRepository;
    }

    public function get($request)
    {

        # INITIALIZE PARAMETERS START
        $month = date('Y-m');

        # on client program tab
        $cp_filter['qdate'] = $request->get('qdate') ?? date('Y-m');
        if ($request->get('cp-month')) { # format Y-m
            $cp_filter['qdate'] = $request->get('cp-month');
        }

        # if (admin)
        # then null
        # elif not admin
        # their uuid
        if ($request->get('quser')) {
            $cp_filter['quuid'] = $request->get('quser');
        }

        $dateDetails = [
            'startDate' => $cp_filter['qdate'] . '-01',
            'endDate' => $cp_filter['qdate'] . '-31'
        ];

        $programId = null; # means all programs

        $cp_filter['queryParams_year1'] = date('Y') - 1;
        $cp_filter['queryParams_year2'] = (int) date('Y');
        $cp_filter['qyear'] = 'current';
        $events = [];
        # INITIALIZE PARAMETERS END


        # fetching client status data
        $response_ofClientStatus = $this->clientStatus($month);

        # fetching all employee data
        $employees = $this->userRepository->getAllUsersByDepartmentAndRole('employee', 'Client Management');

        # fetching chart data by no program (all)
        $totalAllClientProgramByStatus = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => null] + $cp_filter);

        # fetching chart data by admission mentoring
        $admissionsMentoring = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Admissions Mentoring'] + $cp_filter);

        # fetching chart data by initial consultation
        $initialConsultation = $this->clientProgramRepository->getInitialConsultationInformation($cp_filter);

        # sum total initial consultation by summing the data
        $totalInitialConsultation = array_sum($initialConsultation);

        # fetching only already from initial consultation chart data
        $already = $initialConsultation[1];

        # fetching only success program from initial consultation chart data
        $successProgram = $initialConsultation[2];

        # get initial assessment making (average days)
        $initialAssessmentMaking = $this->clientProgramRepository->getInitialMaking($dateDetails, $cp_filter);

        # get conversion time progress (average days)
        $conversionTimeProgress = $this->clientProgramRepository->getConversionTimeProgress($dateDetails, $cp_filter);

        # get initial consultation success percentage 
        $successPercentage = $successProgram == 0 ? 0 : ($successProgram / $totalInitialConsultation) * 100;

        # get total revenue of admission mentoring program
        $totalRevenueAdmMentoringByProgramAndMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Admissions Mentoring'] + $cp_filter);

        # fetching successful programs
        $allSuccessProgramByMonth = $this->clientProgramRepository->getSuccessProgramByMonth($cp_filter);

        # fetching chart data by academic & test preparation program
        $academicTestPrep = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Academic & Test Preparation'] + $cp_filter);

        # get total revenue by academic & test preparation program
        $totalRevenueAcadTestPrepByMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Academic & Test Preparation'] + $cp_filter);

        # fetching chart data by career exploration program
        $careerExploration = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Career Exploration'] + $cp_filter);

        # get total revenue by career exploration program
        $totalRevenueCareerExplorationByMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Career Exploration'] + $cp_filter);

        # fetching chart data of lead source as a whole (admission mentoring, academic & test preparation, career exploration)
        $leadSource = $this->clientProgramRepository->getLeadSource($dateDetails, $cp_filter);

        # fetching chart data of conversion leads as a whole (admission mentoring, academic & test preparation, career exploration)
        $conversionLeads = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter);

        # fetching chart data of conversion lead by admission mentoring program
        $admissionMentoringConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter + ['prog' => 'Admissions Mentoring']);

        # fetching chart data of conversion lead by academic & test preparation program
        $academicTestPrepConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter + ['prog' => 'Academic & Test Preparation']);

        # fetching chart data of conversion lead by career exploration program
        $careerExplorationConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter + ['prog' => 'Career Exploration']);

        # fetching the target sales
        $salesTarget = $this->salesTargetRepository->getMonthlySalesTarget($programId, $cp_filter);

        # fetching the actual sales
        $salesActual = $this->salesTargetRepository->getMonthlySalesActual($programId, $cp_filter);

        # fetching the detail of target sales & actual sales by month
        $salesDetail = $this->salesTargetRepository->getSalesDetail($programId, $cp_filter);

        # fetching all programs for a list that can be choose by user
        $allPrograms = $this->programRepository->getAllPrograms()->groupBy('main_prog.prog_name');

        # fetching all programs including revenue per program yearly
        $comparisons = $this->clientProgramRepository->getComparisonBetweenYears($cp_filter);


        if ($this->eventRepository->getEventsWithParticipants($cp_filter)->count() > 0) {
            $events = $this->eventRepository->getEventsWithParticipants($cp_filter);
            $cp_filter['eventId'] = $events[0]->event_id;
        }

        # fetching conversion lead by client event
        $conversion_lead_of_event = $this->clientEventRepository->getConversionLead($cp_filter);

        return $response_ofClientStatus + [

            # client program tab
            'employees' => $employees,
            'clientProgramGroupByStatus' => $totalAllClientProgramByStatus,
            'admissionsMentoring' => $admissionsMentoring,
            'initialConsultation' => $initialConsultation,
            'totalInitialConsultation' => $totalInitialConsultation,
            'already' => $already,
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
            'conversion_lead_of_event' => $conversion_lead_of_event,
        ];
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
}

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
    }

    public function get($request)
    {

        # Alarm
        $salesAlarm = false;
        $triggerEvent = false;
        $fullDay = Carbon::now()->daysInMonth;
        $midOfMonth = floor($fullDay / 2);

        $leedNeeded = 36;
        $referralTarget = 10;

        $today = date('Y-m-d');
        $currMonth = date('m');

        $allTarget = $this->targetSignalRepository->getAllTargetSignal();
        $dataSalesTarget = $this->targetTrackingRepository->getTargetTrackingMonthlyByDivisi($today, 'Sales');
        $dataReferralTarget = $this->targetTrackingRepository->getTargetTrackingMonthlyByDivisi($today, 'Referral');
        
        $leadSalesTarget = [
            'ic' => 0,
            'hot_lead' => 0,
            'lead_needed' => 0,
            'contribution' => 0,
            'percentage_lead_needed' => 0,
            'percentage_hot_lead' => 0,
            'percentage_ic' => 0,
            'percentage_contribution' => 0,
        ];
        if($dataSalesTarget->count() > 0){
            $leadSalesTarget = [
                'ic' => $dataSalesTarget->target_initconsult,
                'hot_lead' => $dataSalesTarget->target_hotleads,
                'lead_needed' => $dataSalesTarget->target_lead,
                'contribution' => $dataSalesTarget->contribution_target,
            ];
        }

        # referral
        $leadReferralTarget = [
            'ic' => 0,
            'hot_lead' => 0,
            'lead_needed' => 0,
            'contribution' => 0,
            'percentage_lead_needed' => 0,
            'percentage_hot_lead' => 0,
            'percentage_ic' => 0,
            'percentage_contribution' => 0,
        ];
        if($dataReferralTarget->count() > 0){
            $leadReferralTarget = [
                'ic' => $dataReferralTarget->target_initconsult,
                'hot_lead' => $dataReferralTarget->target_hotleads,
                'lead_needed' => $dataReferralTarget->target_lead,
                'contribution' => $dataReferralTarget->contribution_target,
            ];
        }
            

        // $revenueTarget = $dataSalesTarget->total_target;
        $revenueTarget = 0;

        $clientLeadSales = $dataSalesTarget;
        $clientLeadReferral = $dataReferralTarget;

        # LS005 is Referral
        $totalReferralLead = 0;

        $revenue = null;
        $totalRevenue = $revenue != null ? $revenue->sum('total') : 0;

        $actualLeadsSales = [
            'lead_needed' => $clientLeadSales->count() > 0 ? $clientLeadSales->achieved_lead : 0,
            'hot_lead' => $clientLeadSales->count() > 0 ? $clientLeadSales->achieved_hotleads : 0,
            'IC' => $clientLeadSales->count() > 0 ? $clientLeadSales->achieved_initconsult : 0,
            'referral' => $totalReferralLead,
            'revenue' => $totalRevenue,
            'contribution' => 0,
        ];

        # referral
        $actualLeadsReferral = [
            'lead_needed' => $clientLeadReferral->count() > 0 ? $clientLeadReferral->achieved_lead : 0,
            'hot_lead' => $clientLeadReferral->count() > 0 ? $clientLeadReferral->achieved_hotleads : 0,
            'IC' => $clientLeadReferral->count() > 0 ? $clientLeadReferral->achieved_initconsult : 0,
            'referral' => 0,
            'revenue' => 0,
            'contribution' => 0,
        ];

        # Day 1-14 (awal bulan)
        $salesAlarm['mid']['lead_needed'] = $actualLeadsSales['lead_needed'] < $leadSalesTarget['lead_needed'] ? true : false;
        $salesAlarm['mid']['hot_lead'] = $actualLeadsSales['hot_lead'] < $leadSalesTarget['hot_lead'] ? true : false;
        $salesAlarm['mid']['referral'] = $actualLeadsSales['referral'] < $referralTarget ? true : false;

        $triggerEvent = $salesAlarm['mid']['hot_lead'] || $salesAlarm['mid']['referral'] ? true : false;


        # Day 15-30 (akhir bulan)
        if (date('Y-m-d') > date('Y-m') . '-' . $midOfMonth) {
            unset($salesAlarm['mid']['lead_needed']);
            $salesAlarm['end']['revenue'] = $actualLeadsSales['revenue'] < $revenueTarget*50/100 ? true : false;
            $salesAlarm['end']['IC'] = $actualLeadsSales['IC'] < $leadSalesTarget['IC'] ? true : false;
            $salesAlarm['end']['lead_needed'] = $actualLeadsSales['lead_needed'] < 2*$leedNeeded ? true : false;
        }

        $dataLeads = [
            'number_of_leads' => $allTarget->sum('lead_needed'), 
            'number_of_hot_leads' => $allTarget->sum('hot_leads_target'), 
            'number_of_ic' => $allTarget->sum('initial_consult_target'), 
            'number_of_contribution' => $allTarget->sum('contribution_to_target'), 
        ];

        $leadSalesTarget['percentage_lead_needed'] = $this->calculatePercentageLead($actualLeadsSales['lead_needed'], $leadSalesTarget['lead_needed']);
        $leadSalesTarget['percentage_hot_lead'] = $this->calculatePercentageLead($actualLeadsSales['hot_lead'], $leadSalesTarget['hot_lead']);
        $leadSalesTarget['percentage_ic'] = $this->calculatePercentageLead($actualLeadsSales['IC'], $leadSalesTarget['ic']);
        $leadSalesTarget['percentage_contribution'] = $this->calculatePercentageLead($actualLeadsSales['contribution'], $leadSalesTarget['contribution']);
        
        $leadReferralTarget['percentage_lead_needed'] = $this->calculatePercentageLead($actualLeadsReferral['lead_needed'], $leadReferralTarget['lead_needed']);
        $leadReferralTarget['percentage_hot_lead'] = $this->calculatePercentageLead($actualLeadsReferral['hot_lead'], $leadReferralTarget['hot_lead']);
        $leadReferralTarget['percentage_ic'] = $this->calculatePercentageLead($actualLeadsReferral['IC'], $leadReferralTarget['ic']);
        $leadReferralTarget['percentage_contribution'] = $this->calculatePercentageLead($actualLeadsReferral['contribution'], $leadReferralTarget['contribution']);
        
        $targetTrackingPeriod = $this->targetTrackingRepository->getTargetTrackingPeriod(Carbon::now()->startOfMonth()->subMonth(3)->toDateString(), $today);
        
        # Chart lead
        $last3month = $currMonth-2;
        for($i=0; $i<3; $i++){
            $dataLeadChart['target'][] = $targetTrackingPeriod->where('month', $last3month)->count() > 0 ? (int)$targetTrackingPeriod->where('month', $last3month)->first()->target : 0;
            $dataLeadChart['actual'][] = $targetTrackingPeriod->where('month', $last3month)->count() > 0 ? (int)$targetTrackingPeriod->where('month', $last3month)->first()->actual : 0;
            $last3month++;
        }
        // return json_encode($dataLeadChart['target']);
        // exit;
        # === end Alarm ===


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

            # alarm
            'salesAlarm' => $salesAlarm,
            'leadSalesTarget' => $leadSalesTarget,
            'leadReferralTarget' => $leadReferralTarget,
            'triggerEvent' => $triggerEvent,
            'actualLeadsSales' => $actualLeadsSales,
            'actualLeadsReferral' => $actualLeadsReferral,
            'dataLeads' => $dataLeads,
            'dataLeadChart' => $dataLeadChart
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

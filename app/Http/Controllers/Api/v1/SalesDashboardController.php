<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\FollowupRepositoryInterface;
use App\Interfaces\SalesTargetRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SalesDashboardController extends Controller
{
    protected ClientRepositoryInterface $clientRepository;
    protected ClientProgramRepositoryInterface $clientProgramRepository;
    protected ClientEventRepositoryInterface $clientEventRepository;
    protected FollowupRepositoryInterface $followupRepository;
    protected SalesTargetRepositoryInterface $salesTargetRepository;
    protected EventRepositoryInterface $eventRepository;

    public function __construct(ClientRepositoryInterface $clientRepository, ClientProgramRepositoryInterface $clientProgramRepository, ClientEventRepositoryInterface $clientEventRepository, FollowupRepositoryInterface $followupRepository, SalesTargetRepositoryInterface $salesTargetRepository, EventRepositoryInterface $eventRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->followupRepository = $followupRepository;
        $this->salesTargetRepository = $salesTargetRepository;
        $this->eventRepository = $eventRepository;

    }

    public function getClientStatus(Request $request)
    {
        
        $month = $request->route('month') ?? date('Y-m');

        try {

            $data = [
                $this->clientRepository->getCountTotalClientByStatus(0, $month), # prospective
                $this->clientRepository->getCountTotalClientByStatus(1, $month), # potential
                $this->clientRepository->getCountTotalClientByStatus(2, $month), # current
                $this->clientRepository->getCountTotalClientByStatus(3, $month), # current
                $this->clientRepository->getAllClientByRole('Mentee', $month)->count(),
                $this->clientRepository->getAllClientByRole('Alumni', $month)->count(),
                $this->clientRepository->getAllClientByRole('Parent', $month)->count(),
                $this->clientRepository->getAllClientByRole('Teacher/Counselor', $month)->count()
            ];

        } catch (Exception $e) {

            Log::error('Failed to get client status '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get client status'
            ], 500);

        }
        
        return response()->json(
            [
                'success' => true,
                'data' => $data
            ]
        );
    }

    public function getFollowUpReminder(Request $request)
    {
        $month = $request->route('month') ?? date('Y-md');
        $title = '';

        try {

            $data['followUpReminder'] = $followUpReminder = $this->followupRepository->getAllFollowupWithin(7, $month);

            $html = '';
            if ($followUpReminder) {

                foreach ($followUpReminder as $key => $detail) {
                    
                    $html .= '<h6>';
                    $opener = "(";
                    $closer = ")";
                        switch(date('d', strtotime($key))-date('d')) {
                            case 0:
                                $title = 'Today';
                                break;
    
                            case 1:
                                $title = 'Tomorrow';
                                break;
    
                            case 2:
                                $title = 'The day after tomorrow';
                                break;
    
                            default:
                                $opener = null;
                                $closer = null;
                                
                        }
                        $html .= $title.' '. $opener . ' '. date('D, d M Y', strtotime($key)). $closer;
                    $html .= '</h6>';
                    $html .= '<div class="overflow-auto mb-3" style="height: 150px">';
                        $html .= '<ul class="list-group">';
                            foreach($detail as $key => $info) {
                                
                                $checked = $info->status == 1 ? "checked" : null;
    
                                $html .= '<li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="">
                                                <p class="m-0 p-0 lh-1">'.$info->clientProgram->client->full_name.'</p>
                                                    <small class="m-0">'.$info->clientProgram->program_name.'</small>
                                            </div>
                                            <div class="">
                                                <input class="form-check-input me-1" type="checkbox" value="1" '.$checked.
                                                    ' id="mark_'.$key.'"
                                                    data-student="'.$info->clientProgram->client->id.'"
                                                    data-program="'.$info->clientProgram->clientprog_id.'"
                                                    data-followup="'.$info->id.'"
                                                    onchange="marked('.$key.')">
                                                <label class="form-check-label" for="mark_'.$key.'">Done</label>
                                        </div></li></ul></div><hr>';
                            }
                }

            } else {

                $html = 'No Follow up reminder';

            }
            $data['html_txt'] = $html;

        } catch (Exception $e) {

            Log::error('Failed to get follow-up reminder '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get follow-up reminder'
            ], 500);

        }
        
        return response()->json(
            [
                'success' => true,
                'data' => $data
            ]
        );
    }

    public function getMenteesBirthdayByMonth(Request $request)
    {
        $month = $request->route('month');
        
        try {

            $data = $this->clientRepository->getMenteesBirthdayMonthly($month);

        } catch (Exception $e) {

            Log::error('Failed to get mentees birthday '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get mentees birthday'
            ], 500);

        }

        return response()->json(
            [
                'success' => true,
                'data' => $data
            ]
        );

        # old
        if ($data = $this->clientRepository->getMenteesBirthdayMonthly($month)) {
            $response = [
                'success' => true,
                'data' => $data
            ];
        } else {
            $response = [
                'success' => false,
                'data' => null
            ];
        }

        return response()->json($response);
    }

    public function getClientProgramByMonth(Request $request)
    {
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;
        
        try {

            $totalAllClientProgramByStatus = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => null] + $cp_filter);
            
        } catch (Exception $e) {

            Log::error('Failed to get client program dashboard data '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get client program'
            ], 500);

        }

        return response()->json(
            [
                'success' => true,
                'data' => $totalAllClientProgramByStatus
            ]
        );
    }

    public function getSuccessfulProgramByMonth(Request $request)
    {
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        try {

            $html = '';
            if (!$allSuccessProgramByMonth = $this->clientProgramRepository->getSuccessProgramByMonth($cp_filter)) {
                $html .= "There's no success programs";

            } else {
                
                foreach ($allSuccessProgramByMonth as $program) {
                    $html .= '<li class="list-group-item d-flex justify-content-between">
                                <div class="">'.$program->program_name_st.'</div>
                                <span class="badge badge-primary">'.$program->total_client_per_program.'</span>
                            </li>';
                }
            }
            

        } catch (Exception $e) {

            Log::error('Failed to get success program dashboard data '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get success program'
            ], 500);

        }

        return response()->json(
            [
                'success' => true,
                'data' => [
                    'html_txt' => $html
                ]
            ]
        );

    }

    public function getAdmissionsProgramByMonth(Request $request)
    {
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        try {

            $admissionsMentoring = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Admissions Mentoring'] + $cp_filter);

        } catch (Exception $e) {

            Log::error('Failed to get admission program dashboard data '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get admission program'
            ], 500);

        }
        
        return response()->json(
            [
                'success' => true,
                'data' => $admissionsMentoring
            ]
        );
    }

    public function getInitialConsultationByMonth(Request $request)
    {
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        $dateDetails = [
            'startDate' => $cp_filter['qdate'].'-01', 
            'endDate' => $cp_filter['qdate'].'-31'
        ];
        try {

            $admissionsMentoring = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Admissions Mentoring'] + $cp_filter);
            $initialConsultation = $this->clientProgramRepository->getInitialConsultationInformation($cp_filter);
            $totalInitialConsultation = array_sum($initialConsultation);
            $successProgram = $admissionsMentoring[2];
    
            $initialAssessmentMaking = $this->clientProgramRepository->getInitialMaking($dateDetails, $cp_filter);
            $conversionTimeProgress = $this->clientProgramRepository->getConversionTimeProgress($dateDetails, $cp_filter);
            $successPercentage = $successProgram == 0 ? 0 : ($successProgram/$totalInitialConsultation) * 100;
            $totalRevenueAdmMentoringByProgramAndMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Admissions Mentoring'] + $cp_filter);

        } catch (Exception $e) {

            Log::error('Failed to get initial consultation dashboard data '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get initial consult data'
            ], 500);

        }

        # declare new variable
        $initial_assessment_making = isset($initialAssessmentMaking) ? (int) $initialAssessmentMaking->initialMaking : 0;
        $conversion_time = isset($conversionTimeProgress)? (int) $conversionTimeProgress->conversionTime : 0;


        return response()->json(
            [
                'success' => true,
                'data' => [
                    'ctx' => $initialConsultation,
                    'details' => [
                        $totalInitialConsultation,
                        $successProgram,
                        $initial_assessment_making.' Days',
                        $conversion_time.' Days',
                        round($successPercentage).' %',
                        'Rp. '.number_format($totalRevenueAdmMentoringByProgramAndMonth,'2',',','.')
                    ]
                ]
            ]
        );
    }

    public function getAcademicPrepByMonth(Request $request)
    {
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        try {

            $academicTestPrep = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Academic & Test Preparation'] + $cp_filter);
            $totalRevenueAcadTestPrepByMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Academic & Test Preparation'] + $cp_filter);

        } catch (Exception $e) {

            Log::error('Failed to get academic prep dashboard data '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get academic prep data'
            ], 500);

        }

        return response()->json(
            [
                'success' => true,
                'data' => [
                    'ctx' => $academicTestPrep,
                    'total_revenue' => 'Rp. '.number_format($totalRevenueAcadTestPrepByMonth,'2',',','.')
                ]
            ]
        );
        
    }

    public function getCareerExplorationByMonth(Request $request)
    {
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        try {

            $careerExploration = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Career Exploration'] + $cp_filter);
            $totalRevenueCareerExplorationByMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Career Exploration'] + $cp_filter);

        } catch (Exception $e) {

            Log::error('Failed to get career exploration dashboard data '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get career exploration data'
            ], 500);

        }

        return response()->json(
            [
                'success' => true,
                'data' => [
                    'ctx' => $careerExploration,
                    'total_revenue' => 'Rp. '.number_format($totalRevenueCareerExplorationByMonth,'2',',','.')
                ]
            ]
        );
    }

    public function getConversionLeadByMonth(Request $request)
    {
        $dataset_leadsource_labels = $dataset_leadsource = $dataset_conversionlead_labels = $dataset_conversionlead = [];
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        $dateDetails = [
            'startDate' => $cp_filter['qdate'].'-01', 
            'endDate' => $cp_filter['qdate'].'-31'
        ];

        try {

            $leadSource = $this->clientProgramRepository->getLeadSource($dateDetails, $cp_filter);
            foreach ($leadSource as $source) {
                $dataset_leadsource_labels[] = $source->lead_source;
                $dataset_leadsource[] = $source->lead_source_count;
            }

            $conversionLeads = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter);
            foreach ($conversionLeads as $source) {
                $dataset_conversionlead_labels[] = $source->conversion_lead;
                $dataset_conversionlead[] = $source->conversion_lead_count;
            }

        } catch (Exception $e) {

            Log::error('Failed to get conversion lead dashboard data '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get conversion lead data'
            ], 500);

        }

        return response()->json(
            [
                'success' => true,
                'data' => [
                    'ctx_leadsource' => [
                        'label' => $dataset_leadsource_labels,
                        'dataset' => $dataset_leadsource,
                    ],
                    'ctx_conversionlead' => [
                        'label' => $dataset_conversionlead_labels,
                        'dataset' => $dataset_conversionlead,

                    ]
                ]
            ]
        );
        
    }

    public function getLeadAdmissionsProgramByMonth(Request $request)
    {
        $dataset_lead_labels = $dataset_lead = [];
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;
        $cp_filter['prog'] = 'Admissions Mentoring';

        $dateDetails = [
            'startDate' => $cp_filter['qdate'].'-01', 
            'endDate' => $cp_filter['qdate'].'-31'
        ];

        try {

            $adminssionMentoringConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter);
            foreach ($adminssionMentoringConvLead as $source) {

                $dataset_lead_labels[] = $source->conversion_lead;
                $dataset_lead[] = $source->conversion_lead_count;
            }

        } catch (Exception $e) {

            Log::error('Failed to get admission mentoring lead dashboard data '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get admission mentoring lead data'
            ], 500);

        }

        return response()->json(
            [
                'success' => true,
                'data' => [
                    'ctx' => [
                        'label' => $dataset_lead_labels,
                        'dataset' => $dataset_lead,
                    ]
                ]
            ]
        );
        
    }

    public function getLeadAcademicPrepByMonth(Request $request)
    {
        $dataset_lead_labels = $dataset_lead = [];
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;
        $cp_filter['prog'] = 'Academic & Test Preparation';

        $dateDetails = [
            'startDate' => $cp_filter['qdate'].'-01', 
            'endDate' => $cp_filter['qdate'].'-31'
        ];

        try {

            $academicTestPrepConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter);
            foreach ($academicTestPrepConvLead as $source) {

                $dataset_lead_labels[] = $source->conversion_lead;
                $dataset_lead[] = $source->conversion_lead_count;
            }

        } catch (Exception $e) {

            Log::error('Failed to get academic prep lead dashboard data '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get academic prep lead data'
            ], 500);

        }

        return response()->json(
            [
                'success' => true,
                'data' => [
                    'ctx' => [
                        'label' => $dataset_lead_labels,
                        'dataset' => $dataset_lead,
                    ]
                ]
            ]
        );
    }

    public function getLeadCareerExplorationByMonth(Request $request)
    {
        $dataset_lead_labels = $dataset_lead = [];
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;
        $cp_filter['prog'] = 'Career Exploration';

        $dateDetails = [
            'startDate' => $cp_filter['qdate'].'-01', 
            'endDate' => $cp_filter['qdate'].'-31'
        ];

        try {

            $careerExplorationConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter);
            foreach ($careerExplorationConvLead as $source) {

                $dataset_lead_labels[] = $source->conversion_lead;
                $dataset_lead[] = $source->conversion_lead_count;
            }

        } catch (Exception $e) {

            Log::error('Failed to get career exploration lead dashboard data '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get career exploration lead data'
            ], 500);

        }

        return response()->json(
            [
                'success' => true,
                'data' => [
                    'ctx' => [
                        'label' => $dataset_lead_labels,
                        'dataset' => $dataset_lead,
                    ]
                ]
            ]
        );
    }

    public function getAllProgramTargetByMonth(Request $request)
    {   
        $dataset_participant = $dataset_revenue = [];
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        $programId = null; # means all programs
        $salesTarget = $this->salesTargetRepository->getMonthlySalesTarget($programId, $cp_filter);

        $salesActual = $this->salesTargetRepository->getMonthlySalesActual($programId, $cp_filter);
        
        $participant_target = isset($salesTarget->total_participant) ? $salesTarget->total_participant : 0; 
        $participant_actual = isset($salesActual->total_participant) ? $salesActual->total_participant : 0; 
        $revenue_target = isset($salesTarget->total_target) ? $salesTarget->total_target : 0;
        $revenue_actual = isset($salesActual->total_target) ? $salesActual->total_target : 0;


        $dataset_participant = [$participant_target, $participant_actual];
        $dataset_revenue = [$revenue_target, $revenue_actual];
        
        $salesDetail = $this->salesTargetRepository->getSalesDetail($programId, $cp_filter);
        $html = '';
        $no = 1;
        foreach ($salesDetail as $detail) {
            $percentage_participant = $detail->total_target_participant != 0 ? ($detail->total_actual_participant/$detail->total_target_participant) * 100 : 0;
            $percentage_revenue = $detail->total_target_amount != 0 ? ($detail->total_actual_amount/$detail->total_target_amount) * 100 : 0;

            $html .= '<tr class="text-center">
                    <td>'.$no++.'</td>
                    <td>'.$detail->prog_id.'</td>
                    <td class="text-start">'.$detail->program_name_sales.'</td>
                    <td>'.$detail->total_target_participant.'</td>
                    <td>'.number_format($detail->total_target_amount,'2',',','.').'</td>
                    <td>'.$detail->total_actual_participant.'</td>
                    <td>'.number_format($detail->total_actual_amount,'2',',','.').'</td>
                    <td>'.$percentage_participant.'%</td>
                    <td>'.$percentage_revenue.'%</td>
                </tr>';
        }
        
        return response()->json(
            [
                'success' => true,
                'data' => [
                    'dataset' => [
                        'participant' => $dataset_participant,
                        'revenue' => $dataset_revenue,
                    ],
                    'html_txt' => $html
                ]
            ]
        );
    }

    public function getClientEventByYear(Request $request)
    {
        $dataset_participants = $dataset_target = $dataset_labels = $dataset_lead_labels = $dataset_lead_total = [];
        $filter['qyear'] = $request->route('year');
        $filter['quuid'] = $request->route('user') ?? null;

        $html = '';
        if (!$events = $this->eventRepository->getEventsWithParticipants($filter)) {

            $html = '<tr><td colspan="2">There\'s no data</td></tr>';
            $dataset_participant[] = $dataset_target[] = $dataset_labels[] = 0;

        } else {

            foreach ($events as $event) {
                $dataset_participants[] = $event->participants;
                $dataset_target[] = $event->event_target == null ? 0 : $event->event_target;
                $dataset_labels[] = $event->event_title;
    
                $percentage = $event->participants != 0 && $event->event_target != null ? ($event->participants/$event->event_target)*100 : 0;
    
                $html .= '<tr>
                            <td>'.$event->event_title.'</td>
                            <td class="text-end">'.$percentage.'%</td>
                        </tr>';
            }
        }
        


        $filter['eventId'] = count($events) > 0 ? $events[0]->event_id : null;

        if (!$conversion_lead_of_event = $this->clientEventRepository->getConversionLead($filter)) {

            $dataset_lead_labels[] = $dataset_lead_total[] = 0;

        } else {

            foreach ($conversion_lead_of_event->pluck('conversion_lead')->toArray() as $key => $value) {
                $dataset_lead_labels[] = $value;
            }
    
            foreach ($conversion_lead_of_event->pluck('count_conversionLead')->toArray() as $key => $value) {
                $dataset_lead_total[] = $value == null || $value == '' ? 0 : $value;
            }
        }


        return response()->json(
            [
                'success' => true,
                'data' => [
                    'html_txt' => $html,
                    'ctx' => [
                        'participants' => $dataset_participants,
                        'target' => $dataset_target,
                        'labels' => $dataset_labels,
                    ],
                    'lead' => [
                        'labels' => $dataset_lead_labels,
                        'total' => $dataset_lead_total,
                    ]
                ]
            ]
        );
    }
    
    # 
    public function compare_program(Request $request)
    {
        $query_programs_array = explode(',', $request->prog);
        $query_year_1 = $request->first_year;
        $query_year_2 = $request->second_year;
        $user = $request->u;
        
        $cp_filter = [
            'qprogs' => $query_programs_array,
            'queryParams_year1' => $query_year_1,
            'queryParams_year2' => $query_year_2,
            'quuid' => $user == 'all' ? null : $user,
        ];

        try {

            $comparisons = $this->clientProgramRepository->getComparisonBetweenYears($cp_filter);

        } catch (Exception $e) {

            Log::error($e->getMessage());

            return response()->json(['success' => false, 'data' => null]);
        }
        
        return response()->json(['success' => true, 'data' => $comparisons]);
    }

    public function getConversionLeadsByEventId(Request $request)
    {
        $eventId = $request->route('event');
        
    }
}

<?php

namespace App\Http\Controllers\Api\v1;

use App\Enum\LogModule;
use App\Exports\DataClient;
use App\Http\Controllers\Controller;
use App\Interfaces\ClientEventRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\FollowupRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SalesTargetRepositoryInterface;
use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Services\Log\LogService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class SalesDashboardController extends Controller
{
    protected ClientRepositoryInterface $clientRepository;
    protected ClientProgramRepositoryInterface $clientProgramRepository;
    protected ClientEventRepositoryInterface $clientEventRepository;
    protected FollowupRepositoryInterface $followupRepository;
    protected SalesTargetRepositoryInterface $salesTargetRepository;
    protected EventRepositoryInterface $eventRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        ClientProgramRepositoryInterface $clientProgramRepository,
        ClientEventRepositoryInterface $clientEventRepository,
        FollowupRepositoryInterface $followupRepository,
        SalesTargetRepositoryInterface $salesTargetRepository,
        EventRepositoryInterface $eventRepository,
        ProgramRepositoryInterface $programRepository,
        ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository
    ) {
        $this->clientRepository = $clientRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->clientEventRepository = $clientEventRepository;
        $this->followupRepository = $followupRepository;
        $this->salesTargetRepository = $salesTargetRepository;
        $this->eventRepository = $eventRepository;
        $this->programRepository = $programRepository;
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
    }

    public function fnGetClientByMonthAndType(Request $request, LogService $log_service)
    {
        $month = null;
        if ($request->route('month') != "all")
            $month = $request->route('month');


        $type = $request->route('type');
        $as_datatables = false;
        $title = str_replace('-', ' ', $type) . ' Client';

        try {

            switch ($type) {
                case "new-leads":
                    $clients = $this->clientRepository->getClientListByCategoryBasedOnClientLogs('new-lead', $month);
                    if ($month != null) {
                        $last_month = date('Y-m', strtotime('-1 month', strtotime($month)));
                        $clients = $clients->merge($this->clientRepository->getClientListByCategoryBasedOnClientLogs('new-lead', $last_month));
                    }
                    $client_type = 0;
                    break;
    
                case "potential":
                    $clients = $this->clientRepository->getClientListByCategoryBasedOnClientLogs('potential', $month);
                    if ($month != null) {
                        $last_month = date('Y-m', strtotime('-1 month', strtotime($month)));
                        $clients = $clients->merge($this->clientRepository->getClientListByCategoryBasedOnClientLogs('potential', $last_month));
                    }
                    $client_type = 1;
                    break;
    
                case "existing-mentees":
                    $clients = $this->clientRepository->getClientListByCategoryBasedOnClientLogs('mentee', $month);
                    if ($month != null) {
                        $last_month = date('Y-m', strtotime('-1 month', strtotime($month)));
                        $clients = $clients->merge($this->clientRepository->getClientListByCategoryBasedOnClientLogs('mentee', $last_month));
                    }
                    $client_type = 2;
                    break;
    
                case "existing-non-mentees":
                    $clients = $this->clientRepository->getClientListByCategoryBasedOnClientLogs('non-mentee', $month);
                    if ($month != null) {
                        $last_month = date('Y-m', strtotime('-1 month', strtotime($month)));
                        $clients = $clients->merge($this->clientRepository->getClientListByCategoryBasedOnClientLogs('non-mentee', $last_month));
                    }
                    $client_type = 3;
                    break;
    
                    # both alumni-mentee & alumni-non-mentee
                    # never find alumni by month
                case "alumni-mentee":
                    $clients = $this->clientRepository->getClientListByCategoryBasedOnClientLogs('alumni-mentee', $month);
                    if ($month != null) {
                        $last_month = date('Y-m', strtotime('-1 month', strtotime($month)));
                        $clients = $clients->merge($this->clientRepository->getClientListByCategoryBasedOnClientLogs('alumni-mentee', $last_month));
                    }
                    $client_type = 'alumni';
                    break;
    
                case "alumni-non-mentee":
                    $clients = $this->clientRepository->getClientListByCategoryBasedOnClientLogs('alumni-non-mentee', $month);
                    if ($month != null) {
                        $last_month = date('Y-m', strtotime('-1 month', strtotime($month)));
                        $clients = $clients->merge($this->clientRepository->getClientListByCategoryBasedOnClientLogs('alumni-non-mentee', $last_month));
                    }
                    $client_type = 'alumni';
                    break;
    
                case "parent":
                    $clients = $this->clientRepository->getParents($as_datatables, $month, []);
                    $client_type = 'parent';
                    break;
    
                case "teacher-counselor":
                    $clients = $this->clientRepository->getTeachers($as_datatables, $month);
                    $client_type = 'teacher/counselor';
                    break;
    
                default:
                    $title = $client_type = $type;
            }
    
            $index = 1;
            $html = '';
            if ($clients->count() == 0)
                return response()->json(['title' => 'List of ' . ucwords(str_replace('-', ' ', $title)), 'html_ctx' => '<tr align="center"><td colspan="5">No ' . str_replace('-', ' ', $title) . ' data</td></tr>']);
    
            foreach ($clients as $client) {
    
                $client_register_date = date('Y-m', strtotime($client->created_at));
    
                if ($month == null)
                    $month = date('Y-m-d');
    
                $now = date('Y-m', strtotime($month));
                $styling = $client_register_date == $now ? 'class="bg-primary text-white popup-modal-detail-client"' : 'class="popup-modal-detail-client"';
    
                $html .= '<tr ' . $styling . ' data-detail="' . $client->id . '">
                                <td>' . $index++ . '</td>
                                <td>' . $client->full_name . '</td>
                                <td class="text-center">' . $client->pic_name . '</td>
                                <td>' . $client->mail . '</td>
                                <td>' . $client->phone . '</td>
                                <td>' . $client->graduation_year_real . '</td>
                                <td>' . $client->triggered_by . '</td>
                                <td>' . $client->lead_source_log . '</td>
                                <td>' . date('D, d M Y', strtotime($client->created_at))  . '</td>
                            </tr>';
            }
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_CLIENT_BY_MONTH_AND_TYPE, $e->getMessage(), $e->getLine(), $e->getFile());
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_CLIENT_BY_MONTH_AND_TYPE, 'Fetch client `'.$client_type.'` by month and type success', $clients->toArray());
        return response()->json(
            [
                'title' => 'List of ' . ucwords($title),
                'html_ctx' => $html
            ]
        );
    }

    public function fnGetClientStatus(Request $request, LogService $log_service)
    {

        if ($request->route('month') != "all") {
            $month = $request->route('month') ?? date('Y-m');
            $last_month = date('Y-m', strtotime('-1 month', strtotime($month)));
            $type = 'monthly';
        } else {

            $last_month = null;
            $month = date('Y-m');
            $type = 'all';
        }

        try {

            $asDatatables = $groupBy = false;
            $last_month_prospective_client =  $this->clientRepository->countClientByCategory('new-lead', $last_month);
            $monthly_new_prospective_client = $this->clientRepository->countClientByCategory('new-lead', $month);

            $last_month_potential_client = $this->clientRepository->countClientByCategory('potential', $last_month);
            $monthly_new_potential_client= $this->clientRepository->countClientByCategory('potential', $month);

            $last_month_current_client = $this->clientRepository->countClientByCategory('mentee', $last_month);
            $monthly_new_current_client = $this->clientRepository->countClientByCategory('mentee', $month);

            $last_month_completed_client = $this->clientRepository->countClientByCategory('non-mentee', $last_month);
            $monthly_new_completed_client =  $this->clientRepository->countClientByCategory('non-mentee', $month);

            $last_month_parent = $this->clientRepository->countClientByRole('Parent', $last_month);
            $monthly_new_parent = $this->clientRepository->countClientByRole('Parent', $month);

            $last_month_teacher = $this->clientRepository->countClientByRole('Teacher/Counselor', $last_month);
            $monthly_new_teacher = $this->clientRepository->countClientByRole('Teacher/Counselor', $month);

            $data = [
                [
                    'old' => $type == "all" ? $last_month_prospective_client - $monthly_new_prospective_client : $last_month_prospective_client,
                    'new' => $monthly_new_prospective_client,
                    'percentage' => $this->fnCalculatePercentage($type, $last_month_prospective_client, $monthly_new_prospective_client)

                ], # prospective
                [
                    'old' => $type == "all" ? $last_month_potential_client - $monthly_new_potential_client : $last_month_potential_client,
                    'new' => $monthly_new_potential_client,
                    'percentage' => $this->fnCalculatePercentage($type, $last_month_potential_client, $monthly_new_potential_client)
                ], # potential
                [
                    'old' => $type == "all" ? $last_month_current_client - $monthly_new_current_client : $last_month_current_client,
                    'new' => $monthly_new_current_client,
                    'percentage' => $this->fnCalculatePercentage($type, $last_month_current_client, $monthly_new_current_client)
                ], # existing mentee
                [
                    'old' =>  $type == "all" ? $last_month_completed_client - $monthly_new_completed_client : $last_month_completed_client,
                    'new' => $monthly_new_completed_client,
                    'percentage' => $this->fnCalculatePercentage($type, $last_month_completed_client, $monthly_new_completed_client)
                ], # existing non mentee
                [
                    'old' => $type == "all" ? $last_month_parent - $monthly_new_parent : $last_month_parent,
                    'new' => $monthly_new_parent,
                    'percentage' => $this->fnCalculatePercentage($type, $last_month_parent, $monthly_new_parent)
                ], # parent
                [
                    'old' => $type == "all" ? $last_month_teacher - $monthly_new_teacher : $last_month_teacher,
                    'new' => $monthly_new_teacher,
                    'percentage' => $this->fnCalculatePercentage($type, $last_month_teacher, $monthly_new_teacher)
                ] # teacher / counselor
            ];
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_CLIENT_STATUS, $e->getMessage(), $e->getLine(), $e->getFile());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get client status'
            ], 500);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_CLIENT_STATUS, 'Fetch client status success', $data);
        return response()->json(
            [
                'success' => true,
                'data' => $data,
                'type' => $type
            ]
        );
    }

    private function fnCalculatePercentage($type, $last_month_data, $monthly_data)
    {
        switch ($type) {
            case "all":
                # last month data is total data
                if ($last_month_data == 0) {
                    return "0,00";
                }
                return number_format(($monthly_data / ($last_month_data - $monthly_data)) * 100, 2, ',', '.');
                break;

            default:
                if ($monthly_data == 0 && $last_month_data == 0)
                    return "0,00";
                else if ($last_month_data == 0)
                    return number_format((abs($last_month_data - $monthly_data) / $monthly_data) * 100, 2, ',', '.');

                return number_format((abs($last_month_data - $monthly_data) / $last_month_data) * 100, 2, ',', '.');
        }
    }

    public function fnGetFollowUpReminder(Request $request, LogService $log_service)
    {
        $month = $request->route('month') ?? date('Y-md');
        $title = '';

        try {

            $data['followUpReminder'] = $follow_up_reminder = $this->followupRepository->getAllFollowupWithin(7, $month);

            $html = '';
            if ($follow_up_reminder) {

                foreach ($follow_up_reminder as $key => $detail) {

                    $html .= '<h6>';
                    $opener = "(";
                    $closer = ")";
                    switch (date('d', strtotime($key)) - date('d')) {
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
                    $html .= $title . ' ' . $opener . ' ' . date('D, d M Y', strtotime($key)) . $closer;
                    $html .= '</h6>';
                    $html .= '<div class="overflow-auto mb-3" style="height: 150px">';
                    $html .= '<ul class="list-group">';
                    foreach ($detail as $key => $info) {

                        $checked = $info->status == 1 ? "checked" : null;

                        $html .= '<li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="">
                                                <p class="m-0 p-0 lh-1">' . $info->clientProgram->client->full_name . '</p>
                                                    <small class="m-0">' . $info->clientProgram->program->program_name . '</small>
                                            </div>
                                            <div class="">
                                                <input class="form-check-input me-1" type="checkbox" value="1" ' . $checked .
                            ' id="mark_' . $key . '"
                                                    data-student="' . $info->clientProgram->client->id . '"
                                                    data-program="' . $info->clientProgram->clientprog_id . '"
                                                    data-followup="' . $info->id . '"
                                                    onchange="marked(' . $key . ')">
                                                <label class="form-check-label" for="mark_' . $key . '">Done</label>
                                        </div></li></ul></div><hr>';
                    }
                }
            } else {

                $html = 'No Follow up reminder';
            }
            $data['html_txt'] = $html;
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_FOLLOWUP_REMINDER, $e->getMessage(), $e->getLine(), $e->getFile());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get follow-up reminder'
            ], 500);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_FOLLOWUP_REMINDER, 'Fetch follow-up reminder success');
        return response()->json(
            [
                'success' => true,
                'data' => $data
            ]
        );
    }

    public function fnGetMenteesBirthdayByMonth(Request $request, LogService $log_service)
    {
        $month = $request->route('month');

        try {

            $data = $this->clientRepository->getMenteesBirthdayMonthly($month);
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_BIRTHDAY_MENTEE, $e->getMessage(), $e->getLine(), $e->getFile(), ['month' => $month]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get mentees birthday'
            ], 500);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_BIRTHDAY_MENTEE, 'Fetch birthday mentee success', $data->toArray());
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

    public function fnGetClientProgramByMonth(Request $request, LogService $log_service)
    {
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        try {

            $total_all_client_program_by_status = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => null] + $cp_filter);
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_CLIENT_PROGRAM_BY_MONTH, $e->getMessage(), $e->getLine(), $e->getFile(), $cp_filter);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get client program'
            ], 500);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_CLIENT_PROGRAM_BY_MONTH, 'Fetch client program by month success');
        return response()->json(
            [
                'success' => true,
                'data' => $total_all_client_program_by_status
            ]
        );
    }

    public function fnGetSuccessfulProgramByMonth(Request $request, LogService $log_service)
    {
        $cp_filter['progId'] = $request->route('program');
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        try {

            $html = '';
            if (!$all_success_program_by_month = $this->clientProgramRepository->getSuccessProgramByMonth($cp_filter)) {
                $html .= "There's no success programs";
            } else {

                foreach ($all_success_program_by_month as $program) {
                    $html .= '<li class="list-group-item d-flex justify-content-between align-items-center cursor-pointer btn-light detail-success-program" data-prog="' . $program->prog_id . '">
                                <div class="text-start">' . $program->program_name_st . '</div>
                                <span class="badge badge-primary">' . $program->total_client_per_program . '</span>
                            </li>';
                }
            }
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_SUCCESS_CLIENT_PROGRAM_BY_MONTH, $e->getMessage(), $e->getLine(), $e->getFile(), $cp_filter);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get success program'
            ], 500);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_SUCCESS_CLIENT_PROGRAM_BY_MONTH, 'Fetch success client program success');
        return response()->json(
            [
                'success' => true,
                'data' => [
                    'html_txt' => $html
                ]
            ]
        );
    }

    public function fnGetSuccessfulProgramDetailByMonthAndProgram(Request $request, LogService $log_service)
    {
        $cp_filter['progId'] = $request->route('program');
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        try {


            $program = $this->programRepository->getProgramById($cp_filter['progId']);
            $detail_client_joined_program = $this->clientProgramRepository->getDetailSuccessProgramByMonthAndProgram($cp_filter);

            $content = '';
            $html = '<label class="mb-3">Clients joined : <u><br>' . $program->program_name . '</u></label>';

            $no = 1;
            foreach ($detail_client_joined_program as $client) {
                $content .= '<tr>
                        <td>' . $no++ . '.</td>
                        <td>' . $client->full_name . '</td>
                    </tr>';
            }

            $html .= '<table class="table table-striped table-hover">
                    <tr>
                        <th width="8%">No.</th>
                        <th>Client Name</th>
                    </tr>
                    ' . $content . '
                    </table>';
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_SUCCESS_CLIENT_PROGRAM_DETAIL, $e->getMessage(), $e->getLine(), $e->getFile(), $cp_filter);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get detail of success program'
            ], 500);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_SUCCESS_CLIENT_PROGRAM_DETAIL, 'Fetch success client program detail success');
        return response()->json(
            [
                'success' => true,
                'ctx' => $html
            ]
        );
    }

    public function fnGetAdmissionsProgramByMonth(Request $request, LogService $log_service)
    {
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        try {

            $admissions_mentoring = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Admissions Mentoring'] + $cp_filter);
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_ADMISSION_BY_MONTH, $e->getMessage(), $e->getLine(), $e->getFile(), $cp_filter);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get admission program'
            ], 500);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_ADMISSION_BY_MONTH, 'Fetch admission success');
        return response()->json(
            [
                'success' => true,
                'data' => $admissions_mentoring
            ]
        );
    }

    public function fnGetInitialConsultationByMonth(Request $request, LogService $log_service)
    {
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        $date_details = [
            'start' => $cp_filter['qdate'] . '-01',
            'end' => $cp_filter['qdate'] . '-31'
        ];
        try {

            $admissions_mentoring = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Admissions Mentoring'] + $cp_filter);
            $initial_consultation = $this->clientProgramRepository->getInitialConsultationInformation($cp_filter);
            $total_initial_consultation = array_sum($initial_consultation);
            $success_program = $admissions_mentoring[2];
            $already = $initial_consultation[1];

            $initial_assessment_making = $this->clientProgramRepository->getInitialMaking($date_details, $cp_filter);
            $conversion_time_progress = $this->clientProgramRepository->getConversionTimeProgress($date_details, $cp_filter);
            $success_percentage = $success_program == 0 || $total_initial_consultation == 0 ? 0 : ($success_program / $total_initial_consultation) * 100;
            $total_revenue_adm_mentoring_by_program_and_month = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Admissions Mentoring'] + $cp_filter);
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_INITIAL_CONSULT_BY_MONTH, $e->getMessage(), $e->getLine(), $e->getFile(), $cp_filter);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get initial consult data'
            ], 500);
        }

        # declare new variable
        $initial_assessment_making = isset($initial_assessment_making) ? (int) $initial_assessment_making->initialMaking : 0;
        $conversion_time = isset($conversion_time_progress) ? (int) $conversion_time_progress->conversionTime : 0;

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_INITIAL_CONSULT_BY_MONTH, 'Fetch initial consult success');
        return response()->json(
            [
                'success' => true,
                'data' => [
                    'ctx' => $initial_consultation,
                    'details' => [
                        $total_initial_consultation,
                        $already,
                        $success_program,
                        $initial_assessment_making . ' Days',
                        $conversion_time . ' Days',
                        round($success_percentage) . ' %',
                        'Rp. ' . number_format($total_revenue_adm_mentoring_by_program_and_month, '2', ',', '.')
                    ]
                ]
            ]
        );
    }

    public function fnGetAcademicPrepByMonth(Request $request, LogService $log_service)
    {
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        try {

            $academic_test_prep = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Academic & Test Preparation'] + $cp_filter);
            $total_revenue_acad_test_prep_by_month = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Academic & Test Preparation'] + $cp_filter);
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_ACADEMIC_PREP_BY_MONTH, $e->getMessage(), $e->getLine(), $e->getFile(), $cp_filter);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get academic prep data'
            ], 500);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_ACADEMIC_PREP_BY_MONTH, 'Fetch academic prep success');
        return response()->json(
            [
                'success' => true,
                'data' => [
                    'ctx' => $academic_test_prep,
                    'total_revenue' => 'Rp. ' . number_format($total_revenue_acad_test_prep_by_month, '2', ',', '.')
                ]
            ]
        );
    }

    public function fnGetCareerExplorationByMonth(Request $request, LogService $log_service)
    {
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        try {

            $career_exploration = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Experiential Learning'] + $cp_filter);
            $total_revenue_career_exploration_by_month = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Experiential Learning'] + $cp_filter);
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_CAREER_EXPLORATION_BY_MONTH, $e->getMessage(), $e->getLine(), $e->getFile(), $cp_filter);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get career exploration data'
            ], 500);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_CAREER_EXPLORATION_BY_MONTH, 'Fetch career exploration success');
        return response()->json(
            [
                'success' => true,
                'data' => [
                    'ctx' => $career_exploration,
                    'total_revenue' => 'Rp. ' . number_format($total_revenue_career_exploration_by_month, '2', ',', '.')
                ]
            ]
        );
    }

    public function fnGetClientProgramByMonthDetail(Request $request, LogService $log_service)
    {
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;
        $cp_filter['qtype'] = $request->route('type');

        $type = '';

        switch ($cp_filter['qtype']) {
            case 'academic-prep':
                $type = 'Academic & Test Preparation';
                break;
            case 'career-exploration':
                $type = 'Experiential Learning';
                break;
            case 'admissions-mentoring':
                $type = 'Admissions Mentoring';
                break;
        }

        try {

            $client_programs = $this->clientProgramRepository->getClientProgramGroupDataByStatusAndUserArray(['program' => $type] + $cp_filter);

            $html = $table_content = null;
            foreach ($client_programs as $title => $data) {

                if ($data->count() > 0) {

                    foreach ($data as $program_name => $value) {

                        $no = 1;
                        $table_content = ''; # reset table content
                        foreach ($value as $data) {

                            switch ($title) {
                                case "pending":
                                    $style_title = 'text-warning';
                                    $date_name = 'Created At';
                                    $date_value = date('l, d M Y', strtotime($data->created_at));
                                    break;

                                case "failed":
                                    $style_title = 'text-danger';
                                    $date_name = 'Failed Date';
                                    $date_value = date('l, d M Y', strtotime($data->failed_date));
                                    break;

                                case "success":
                                    $style_title = 'text-success';
                                    $date_name = 'Success Date';
                                    $date_value = date('l, d M Y', strtotime($data->success_date));
                                    break;

                                case "refund":
                                    $style_title = 'text-info';
                                    $date_name = 'Refund Date';
                                    $date_value = date('l, d M Y', strtotime($data->refund_date));
                                    break;
                            }
                            $prog = $data->program->prog_program;

                            $table_content .= '
                                <tr>
                                    <td align="center">' . $no++ . '.</td>
                                    <td>' . $data->client->client_name . '</td>
                                    <td align="center">' . $date_value . '</td>
                                </tr>';
                        }

                        $html .=
                            '
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="">
                                <label class="fw-semibold fs-6 ' . $style_title . '">' . ucfirst($title) . '</label>
                                </div>
                                <div class="bg-secondary p-1 px-3 rounded shadow text-white">
                                <label class="">' . $prog . '</label>
                                </div>
                            </div>
                            <table class="table table-hover table-striped my-2">
                                <tr>
                                    <th width="5%">No.</th>
                                    <th>Client Name</th>
                                    <th style="width:150px">' . $date_name . '</th>
                                </tr>
                                ' . $table_content . '
                            </table>';
                    }
                }
            }
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_CLIENT_PROGRAM_DETAIL, $e->getMessage(), $e->getLine(), $e->getFile(), $cp_filter);
            return response()->json(['message' => 'Failed to get detail of client program.']);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_CLIENT_PROGRAM_DETAIL, 'Fetch career exploration detail success');
        return response()->json([
            'success' => true,
            'data' => $client_programs,
            'ctx' => $html
        ]);
    }

    public function fnGetConversionLeadByMonth(Request $request, LogService $log_service)
    {
        $dataset_leadsource_labels = $dataset_leadsource = $dataset_leadsource_bgcolor = $dataset_conversionlead_labels = $dataset_conversionlead = $dataset_conversionlead_bgcolor = [];
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        $date_details = [
            'start' => $cp_filter['qdate'] . '-01',
            'end' => $cp_filter['qdate'] . '-31'
        ];

        try {

            $lead_source = $this->clientProgramRepository->rnGetLeadSource($date_details, $cp_filter);
            foreach ($lead_source as $source) {
                $dataset_leadsource_labels[] = $source->lead_source;
                $dataset_leadsource[] = $source->lead_source_count;
                $dataset_leadsource_bgcolor[] = $source->color_code;
            }

            $conversion_leads = $this->clientProgramRepository->rnGetConversionLead($date_details, $cp_filter);
            foreach ($conversion_leads as $source) {
                $dataset_conversionlead_labels[] = $source->conversion_lead;
                $dataset_conversionlead[] = $source->conversion_lead_count;
                $dataset_conversionlead_bgcolor[] = $source->color_code;
            }
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_CONVERSION_LEAD_BY_MONTH, $e->getMessage(), $e->getLine(), $e->getFile(), $cp_filter);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get conversion lead data'
            ], 500);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_CONVERSION_LEAD_BY_MONTH, 'Fetch conversion lead success');
        return response()->json(
            [
                'success' => true,
                'data' => [
                    'ctx_leadsource' => [
                        'label' => $dataset_leadsource_labels,
                        'dataset' => $dataset_leadsource,
                        'bgcolor' => $dataset_leadsource_bgcolor
                    ],
                    'ctx_conversionlead' => [
                        'label' => $dataset_conversionlead_labels,
                        'dataset' => $dataset_conversionlead,
                        'bgcolor' => $dataset_conversionlead_bgcolor
                    ]
                ]
            ]
        );
    }

    public function fnGetLeadAdmissionsProgramByMonth(Request $request, LogService $log_service)
    {
        $dataset_lead_labels = $dataset_lead = $dataset_bgcolor = [];
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;
        $cp_filter['prog'] = 'Admissions Mentoring';

        $date_details = [
            'start' => $cp_filter['qdate'] . '-01',
            'end' => $cp_filter['qdate'] . '-31'
        ];

        try {

            $conversion_lead_from_admission_mentoring = $this->clientProgramRepository->rnGetConversionLead($date_details, $cp_filter);
            foreach ($conversion_lead_from_admission_mentoring as $source) {

                $dataset_lead_labels[] = $source->conversion_lead;
                $dataset_lead[] = $source->conversion_lead_count;
                $dataset_bgcolor[] = $source->color_code;
            }
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_CONVERSION_LEAD_FROM_ADMISSION_BY_MONTH, $e->getMessage(), $e->getLine(), $e->getFile(), $cp_filter);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get admission mentoring lead data'
            ], 500);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_CONVERSION_LEAD_FROM_ADMISSION_BY_MONTH, 'Fetch conversion lead from admission success');
        return response()->json(
            [
                'success' => true,
                'data' => [
                    'ctx' => [
                        'label' => $dataset_lead_labels,
                        'dataset' => $dataset_lead,
                        'bgcolor' => $dataset_bgcolor
                    ]
                ]
            ]
        );
    }

    public function fnGetLeadAcademicPrepByMonth(Request $request, LogService $log_service)
    {
        $dataset_lead_labels = $dataset_lead = $dataset_bgcolor = [];
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;
        $cp_filter['prog'] = 'Academic & Test Preparation';

        $date_details = [
            'start' => $cp_filter['qdate'] . '-01',
            'end' => $cp_filter['qdate'] . '-31'
        ];

        try {

            $conversion_lead_from_academic_prep = $this->clientProgramRepository->rnGetConversionLead($date_details, $cp_filter);
            foreach ($conversion_lead_from_academic_prep as $source) {

                $dataset_lead_labels[] = $source->conversion_lead;
                $dataset_lead[] = $source->conversion_lead_count;
                $dataset_bgcolor[] = $source->color_code;
            }
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_CONVERSION_LEAD_FROM_ACADEMIC_PREP_BY_MONTH, $e->getMessage(), $e->getLine(), $e->getFile(), $cp_filter);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get academic prep lead data'
            ], 500);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_CONVERSION_LEAD_FROM_ACADEMIC_PREP_BY_MONTH, 'Fetch conversion lead from academic prep success');
        return response()->json(
            [
                'success' => true,
                'data' => [
                    'ctx' => [
                        'label' => $dataset_lead_labels,
                        'dataset' => $dataset_lead,
                        'bgcolor' => $dataset_bgcolor
                    ]
                ]
            ]
        );
    }

    public function fnGetLeadCareerExplorationByMonth(Request $request, LogService $log_service)
    {
        $dataset_lead_labels = $dataset_lead = $dataset_bgcolor = [];
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;
        $cp_filter['prog'] = 'Experiential Learning'; # new

        $date_details = [
            'start' => $cp_filter['qdate'] . '-01',
            'end' => $cp_filter['qdate'] . '-31'
        ];

        try {

            $conversion_lead_from_experiential_learning = $this->clientProgramRepository->rnGetConversionLead($date_details, $cp_filter);
            foreach ($conversion_lead_from_experiential_learning as $source) {

                $dataset_lead_labels[] = $source->conversion_lead;
                $dataset_lead[] = $source->conversion_lead_count;
                $dataset_bgcolor[] = $source->color_code;
            }
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_CONVERSION_LEAD_FROM_EXP_LEARNING_BY_MONTH, $e->getMessage(), $e->getLine(), $e->getFile(), $cp_filter);
            return response()->json([
                'success' => false,
                'message' => 'Failed to get career exploration lead data'
            ], 500);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_CONVERSION_LEAD_FROM_EXP_LEARNING_BY_MONTH, 'Fetch conversion lead from experiential learning success');
        return response()->json(
            [
                'success' => true,
                'data' => [
                    'ctx' => [
                        'label' => $dataset_lead_labels,
                        'dataset' => $dataset_lead,
                        'bgcolor' => $dataset_bgcolor
                    ]
                ]
            ]
        );
    }

    public function fnGetAllProgramTargetByMonth(Request $request, LogService $log_service)
    {
        $dataset_participant = $dataset_revenue = [];
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;
        $program_id = null; # means all programs

        try {

            $sales_target = $this->salesTargetRepository->getMonthlySalesTarget($program_id, $cp_filter);
            $sales_actual = $this->salesTargetRepository->getMonthlySalesActual($program_id, $cp_filter);
    
            $participant_target = isset($sales_target->total_participant) ? $sales_target->total_participant : 0;
            $participant_actual = isset($sales_actual['total_participant']) ? $sales_actual['total_participant'] : 0;
            $revenue_target = isset($sales_target->total_target) ? $sales_target->total_target : 0;
            $revenue_actual = isset($sales_actual['total_target']) ? $sales_actual['total_target'] : 0;
    
    
            $dataset_participant = [$participant_target, $participant_actual];
            $dataset_revenue = [$revenue_target, $revenue_actual];
    
            $sales_detail = $this->salesTargetRepository->getSalesDetail($program_id, $cp_filter);
    
            $html = '';
            $no = 1;
            foreach ($sales_detail as $detail) {
                $percentage_participant = $detail['total_target_participant'] != 0 ? round(($detail['total_actual_participant'] / $detail['total_target_participant']) * 100, 2) : 0;
                $percentage_revenue = $detail['total_target'] != 0 ? ($detail['total_actual_amount'] / $detail['total_target']) * 100 : 0;
    
                $target_student = $detail['total_target_participant'] ??= 0;
    
                $html .= '<tr class="text-center">
                        <td>' . $no++ . '</td>
                        <td>' . (isset($detail['prog_id']) ? $detail['prog_id'] : '-') . '</td>
                        <td class="text-start">' . $detail['program_name_sales'] . '</td>
                        <td>' . $target_student . '</td>
                        <td>' . number_format($detail['total_target'], '2', ',', '.') . '</td>
                        <td>' . $detail['total_actual_participant'] . '</td>
                        <td>' . number_format($detail['total_actual_amount'], '2', ',', '.') . '</td>
                        <td>' . $percentage_participant . '%</td>
                        <td>' . $percentage_revenue . '%</td>
                    </tr>';
            }
    
            $html .= '<tr class="text-center">
                        <th colspan="3">Total</th>
                        <td><b>' . $sales_detail->sum('total_target_participant') . '</b></td>
                        <td><b>' . number_format($sales_detail->sum('total_target'), '2', ',', '.') . '</b></td>
                        <td><b>' . $sales_detail->sum('total_actual_participant') . '</b></td>
                        <td><b>' . number_format($sales_detail->sum('total_actual_amount'), '2', ',', '.') . '</b></td>
                        <td><b>' . ($sales_detail->sum('total_target_participant') != 0 ?  round(($sales_detail->sum('total_actual_participant') / $sales_detail->sum('total_target_participant')) * 100, 2) : 0) . '%</b></td>
                        <td><b>' . ($sales_detail->sum('total_target') != 0 ? ($sales_detail->sum('total_actual_amount') / $sales_detail->sum('total_target')) * 100 : 0) . '%</b></td>
                    </tr>';
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_PROGRAM_TARGET_BY_MONTH, $e->getMessage(), $e->getLine(), $e->getFile(), $cp_filter);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_PROGRAM_TARGET_BY_MONTH, 'Fetch program target success');
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

    public function fnGetClientEventByYear(Request $request, LogService $log_service)
    {
        $dataset_participants = $dataset_target = $dataset_labels = $dataset_lead_labels = $dataset_lead_total = [];
        $filter['qyear'] = $request->route('year');
        $filter['quuid'] = $request->route('user') ?? null;

        try {

            $html = '';
            if (!$events = $this->eventRepository->getEventsWithParticipants($filter)) {
    
                $html = '<tr><td colspan="2">There\'s no data</td></tr>';
                $dataset_participant[] = $dataset_target[] = $dataset_labels[] = 0;
            } else {
    
                foreach ($events as $event) {
                    $dataset_participants[] = $event->participants;
                    $dataset_target[] = $event->event_target == null ? 0 : $event->event_target;
                    $dataset_labels[] = $event->event_title;
    
                    $percentage = $event->participants != 0 && $event->event_target != null ? ($event->participants / $event->event_target) * 100 : 0;
    
                    $html .= '<tr>
                                <td>' . $event->event_title . '</td>
                                <td class="text-end">' . $percentage . '%</td>
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
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_CLIENT_EVENT_YEARLY, $e->getMessage(), $e->getLine(), $e->getFile(), $filter);
        }


        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_CLIENT_EVENT_YEARLY, 'Fetch client event yearly success');
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
    public function fnCompareProgram(Request $request, LogService $log_service)
    {
        # retrieve the data from view
        $queries = $request->only([
            'prog',
            'query_month',
            'first_year',
            'second_year',
            'first_monthyear',
            'second_monthyear',
            'uuid', # user uuid
        ]);

        $cp_filter = [
            'query_use_month' => $queries['query_month'],
            'qprogs' => $queries['prog'] ?? null,
            'qparam_year1' => $queries['first_year'],
            'qparam_year2' => $queries['second_year'],
            'queryParams_monthyear1' => $queries['first_monthyear'],
            'queryParams_monthyear2' => $queries['second_monthyear'],
            'quuid' => $queries['uuid'] == 'all' ? null : $queries['uuid'],
        ];

        try {

            $comparisons = $this->clientProgramRepository->getComparisonBetweenYears($cp_filter);
            
        } catch (Exception $e) {

            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_CLIENT_PROGRAM_COMPARISON, $e->getMessage(), $e->getLine(), $e->getFile(), $cp_filter);
            return response()->json(['success' => false, 'data' => null]);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_CLIENT_PROGRAM_COMPARISON, 'Fetch client program comparison success');
        return response()->json(['success' => true, 'data' => $comparisons]);
    }

    public function fnExportClient()
    {
        return Excel::download(new DataClient(), 'data-client.xlsx');
    }

    public function fnGetDetailInitialConsultByMonth(Request $request, LogService $log_service)
    {
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;
        $cp_filter['count'] = false;

        try {

            $data = $this->clientProgramRepository->getInitialConsultationInformation($cp_filter);
    
            $i = 0;
            while ($i < count($data)) {
                $no = 1;
    
                switch ($i) {
                    case 0:
                        $name = 'soon';
                        break;
                    case 1:
                        $name = 'already';
                        break;
                    case 2:
                        $name = 'success';
                        break;
                }
    
                ${'table_content_'.$name} = null;
                
                if (count($data[$i]) == 0) {
                    ${'table_content_'.$name} .= '<tr><td class="text-center" colspan="3">No data</td></tr>';
                    $i++;
                    continue;
                }
    
    
                foreach ($data[$i] as $detail) {
    
                    ${'table_content_'.$name} .= '<tr>
                                <td>'. $no++ .'</td>
                                <td>'. $detail->client->full_name .'</td>
                                <td>'. date('d F Y', strtotime($detail->initconsult_date)) .'</td>
                            </tr>';
                }
    
                $i++;
            }
    
    
            $html = '<hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="">
                        <label class="fw-semibold fs-6 text-primary">Soon</label>
                    </div>
                </div>
                <table class="table table-hover table-striped my-2">
                    <tr>
                        <th width="5%">No.</th>
                        <th>Client Name</th>
                        <th style="width:150px">IC Date</th>
                    </tr>
                    ' . $table_content_soon . '
                </table>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="">
                        <label class="fw-semibold fs-6 text-danger">Already</label>
                    </div>
                </div>
                <table class="table table-hover table-striped my-2">
                    <tr>
                        <th width="5%">No.</th>
                        <th>Client Name</th>
                        <th style="width:150px">IC Date</th>
                    </tr>
                    ' . $table_content_already . '
                </table>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="">
                        <label class="fw-semibold fs-6 text-warning">Success</label>
                    </div>
                </div>
                <table class="table table-hover table-striped my-2">
                    <tr>
                        <th width="5%">No.</th>
                        <th>Client Name</th>
                        <th style="width:150px">IC Date</th>
                    </tr>
                    ' . $table_content_success . '
                </table>';
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::SALES_DASHBOARD_GET_INITIAL_CONSULT_DETAIL, $e->getMessage(), $e->getLine(), $e->getFile(), $cp_filter);
        }

        $log_service->createSuccessLog(LogModule::SALES_DASHBOARD_GET_INITIAL_CONSULT_DETAIL, 'Fetch initial consult detail');
        return response()->json([
            'success' => true,
            'data' => [
                'ctx' => $html
            ] 
        ]);

    }

}

<?php

namespace App\Http\Controllers\Api\v1;

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

    public function getClientByMonthAndType(Request $request)
    {
        $month = null;
        if ($request->route('month') != "all")
            $month = $request->route('month');


        $type = $request->route('type');
        $asDatatables = false;
        $groupBy = true;
        $title = str_replace('-', ' ', $type) . ' Client';

        switch ($type) {
            case "new-leads":
                $clients = $this->clientRepository->getNewLeads($asDatatables, $month);
                if ($month != null) {
                    $last_month = date('Y-m', strtotime('-1 month', strtotime($month)));
                    $clients = $clients->merge($this->clientRepository->getNewLeads($asDatatables, $last_month));
                }
                $clientType = 0;
                break;

            case "potential":
                $clients = $this->clientRepository->getPotentialClients($asDatatables, $month);
                if ($month != null) {
                    $last_month = date('Y-m', strtotime('-1 month', strtotime($month)));
                    $clients = $clients->merge($this->clientRepository->getPotentialClients($asDatatables, $last_month));
                }
                $clientType = 1;
                break;

            case "existing-mentees":
                $clients = $this->clientRepository->getExistingMentees($asDatatables, $month);
                if ($month != null) {
                    $last_month = date('Y-m', strtotime('-1 month', strtotime($month)));
                    $clients = $clients->merge($this->clientRepository->getExistingMentees($asDatatables, $last_month));
                }
                $clientType = 2;
                break;

            case "existing-non-mentees":
                $clients = $this->clientRepository->getExistingNonMentees($asDatatables, $month);
                if ($month != null) {
                    $last_month = date('Y-m', strtotime('-1 month', strtotime($month)));
                    $clients = $clients->merge($this->clientRepository->getExistingNonMentees($asDatatables, $last_month));
                }
                $clientType = 3;
                break;

                # both alumni-mentee & alumni-non-mentee
                # never find alumni by month
            case "alumni-mentee":
                $clients = $this->clientRepository->getAlumniMentees($groupBy, $asDatatables);
                $clientType = 'alumni';
                break;

            case "alumni-non-mentee":
                $clients = $this->clientRepository->getAlumniNonMentees($groupBy, $asDatatables);
                $clientType = 'alumni';
                break;

            case "parent":
                $clients = $this->clientRepository->getParents($asDatatables, $month);
                $clientType = 'parent';
                break;

            case "teacher-counselor":
                $clients = $this->clientRepository->getTeachers($asDatatables, $month);
                $clientType = 'teacher/counselor';
                break;

            default:
                $title = $clientType = $type;
        }

        $index = 1;
        $html = '';
        if ($clients->count() == 0)
            return response()->json(['title' => 'List of ' . ucwords(str_replace('-', ' ', $title)), 'html_ctx' => '<tr align="center"><td colspan="5">No ' . str_replace('-', ' ', $title) . ' data</td></tr>']);

        # when is mentee    
        # special case because already grouped by year
        # so we need to extract as they are
        if ($clientType == 'alumni') {

            foreach ($clients as $key => $value) {
                $html .= '<tr>
                            <td colspan="5">' . $key . '</td>
                        </tr>';

                foreach ($value as $client) {
                    $client_register_date = date('Y-m', strtotime($client->created_at));
                    $now = date('Y-m');
                    $styling = $client_register_date == $now ? 'class="bg-primary text-white popup-modal-detail-client"' : 'class="popup-modal-detail-client"';

                    $pic_name = isset($client->handleBy) ? $client->handledBy()->first()->full_name : null;

                    $html .= '<tr ' . $styling . ' data-detail="' . $client->id . '">
                                <td>' . $index++ . '</td>
                                <td>' . $client->full_name . '</td>
                                <td>' . $pic_name .'</td>
                                <td>' . $client->mail . '</td>
                                <td>' . $client->phone . '</td>
                                <td>' . $client->graduation_year_real . '</td>
                                <td>' . date('d F Y H:i', strtotime($client->created_at)) . '</td>
                            </tr>';
                }
            }
        } else {
            foreach ($clients as $client) {

                $client_register_date = date('Y-m', strtotime($client->created_at));

                if ($month == null)
                    $month = date('Y-m-d');

                $now = date('Y-m', strtotime($month));
                $styling = $client_register_date == $now ? 'class="bg-primary text-white popup-modal-detail-client"' : 'class="popup-modal-detail-client"';

                $clientsPic = $client->handledBy->first()->fullname ?? "-";

                $html .= '<tr ' . $styling . ' data-detail="' . $client->id . '">
                            <td>' . $index++ . '</td>
                            <td>' . $client->full_name . '</td>
                            <td class="text-center">' . $clientsPic . '</td>
                            <td>' . $client->mail . '</td>
                            <td>' . $client->phone . '</td>
                            <td>' . $client->graduation_year_real . '</td>
                            <td>' . date('D, d M Y', strtotime($client->created_at))  . '</td>
                        </tr>';
            }
        }

        return response()->json(
            [
                'title' => 'List of ' . ucwords($title),
                'html_ctx' => $html
            ]
        );
    }

    public function getClientStatus(Request $request)
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
            $last_month_prospective_client = $this->clientRepository->getNewLeads($asDatatables, $last_month)->count();
            $monthly_new_prospective_client = $this->clientRepository->getNewLeads($asDatatables, $month)->count();

            $last_month_potential_client = $this->clientRepository->getPotentialClients($asDatatables, $last_month)->count();
            $monthly_new_potential_client = $this->clientRepository->getPotentialClients($asDatatables, $month)->count();

            $last_month_current_client = $this->clientRepository->getExistingMentees($asDatatables, $last_month)->count();
            $monthly_new_current_client = $this->clientRepository->getExistingMentees($asDatatables, $month)->count();

            $last_month_completed_client = $this->clientRepository->getExistingNonMentees($asDatatables, $last_month)->count();
            $monthly_new_completed_client = $this->clientRepository->getExistingNonMentees($asDatatables, $month)->count();

            $last_month_alumniMentees = $this->clientRepository->getAlumniMentees($groupBy, $asDatatables, $last_month)->count();
            $monthly_new_alumniMentees = $this->clientRepository->getAlumniMentees($groupBy, $asDatatables, $month)->count();

            $last_month_alumniNonMentees = $this->clientRepository->getAlumniNonMentees($groupBy, $asDatatables, $last_month)->count();
            $monthly_new_alumniNonMentees = $this->clientRepository->getAlumniNonMentees($groupBy, $asDatatables, $month)->count();

            $last_month_parent = $this->clientRepository->getParents($asDatatables, $last_month)->count();
            $monthly_new_parent = $this->clientRepository->getParents($asDatatables, $month)->count();

            $last_month_teacher = $this->clientRepository->getAllClientByRole('Teacher/Counselor', $last_month)->count();
            $monthly_new_teacher = $this->clientRepository->getAllClientByRole('Teacher/Counselor', $month)->count();

            $data = [
                [
                    'old' => $type == "all" ? $last_month_prospective_client - $monthly_new_prospective_client : $last_month_prospective_client,
                    'new' => $monthly_new_prospective_client,
                    'percentage' => $this->calculatePercentage($type, $last_month_prospective_client, $monthly_new_prospective_client)

                ], # prospective
                [
                    'old' => $type == "all" ? $last_month_potential_client - $monthly_new_potential_client : $last_month_potential_client,
                    'new' => $monthly_new_potential_client,
                    'percentage' => $this->calculatePercentage($type, $last_month_potential_client, $monthly_new_potential_client)
                ], # potential
                [
                    'old' => $type == "all" ? $last_month_current_client - $monthly_new_current_client : $last_month_current_client,
                    'new' => $monthly_new_current_client,
                    'percentage' => $this->calculatePercentage($type, $last_month_current_client, $monthly_new_current_client)
                ], # existing mentee
                [
                    'old' =>  $type == "all" ? $last_month_completed_client - $monthly_new_completed_client : $last_month_completed_client,
                    'new' => $monthly_new_completed_client,
                    'percentage' => $this->calculatePercentage($type, $last_month_completed_client, $monthly_new_completed_client)
                ], # existing non mentee
                // [
                //     'old' => $type == "all" ? $last_month_alumniMentees - $monthly_new_alumniMentees : $last_month_alumniMentees,
                //     'new' => $monthly_new_alumniMentees,
                //     'percentage' => $this->calculatePercentage($type, $last_month_alumniMentees, $monthly_new_alumniMentees)
                // ], # alumni-mentee

                // [
                //     'old' => $type == "all" ? $last_month_alumniNonMentees-$monthly_new_alumniNonMentees : $last_month_alumniNonMentees,
                //     'new' => $monthly_new_alumniNonMentees,
                //     'percentage' => $this->calculatePercentage($type, $last_month_alumniNonMentees, $monthly_new_alumniNonMentees)
                // ], # alumni-non-mentee
                [
                    'old' => $type == "all" ? $last_month_parent - $monthly_new_parent : $last_month_parent,
                    'new' => $monthly_new_parent,
                    'percentage' => $this->calculatePercentage($type, $last_month_parent, $monthly_new_parent)
                ], # parent
                [
                    'old' => $type == "all" ? $last_month_teacher - $monthly_new_teacher : $last_month_teacher,
                    'new' => $monthly_new_teacher,
                    'percentage' => $this->calculatePercentage($type, $last_month_teacher, $monthly_new_teacher)
                ] # teacher / counselor
            ];
        } catch (Exception $e) {

            Log::error('Failed to get client status ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get client status'
            ], 500);
        }

        return response()->json(
            [
                'success' => true,
                'data' => $data,
                'type' => $type
            ]
        );
    }

    private function calculatePercentage($type, $last_month_data, $monthly_data)
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

            Log::error('Failed to get follow-up reminder ' . $e->getMessage());
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

            Log::error('Failed to get mentees birthday ' . $e->getMessage());
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

            Log::error('Failed to get client program dashboard data ' . $e->getMessage());
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
        $cp_filter['progId'] = $request->route('program');
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        try {

            $html = '';
            if (!$allSuccessProgramByMonth = $this->clientProgramRepository->getSuccessProgramByMonth($cp_filter)) {
                $html .= "There's no success programs";
            } else {

                foreach ($allSuccessProgramByMonth as $program) {
                    $html .= '<li class="list-group-item d-flex justify-content-between align-items-center cursor-pointer btn-light detail-success-program" data-prog="' . $program->prog_id . '">
                                <div class="text-start">' . $program->program_name_st . '</div>
                                <span class="badge badge-primary">' . $program->total_client_per_program . '</span>
                            </li>';
                }
            }
        } catch (Exception $e) {

            Log::error('Failed to get success program dashboard data ' . $e->getMessage());
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

    public function getSuccessfulProgramDetailByMonthAndProgram(Request $request)
    {
        $cp_filter['progId'] = $request->route('program');
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        try {


            $program = $this->programRepository->getProgramById($cp_filter['progId']);
            $detailClientJoinedProgram = $this->clientProgramRepository->getDetailSuccessProgramByMonthAndProgram($cp_filter);

            $content = '';
            $html = '<label class="mb-3">Clients joined : <u><br>' . $program->program_name . '</u></label>';

            $no = 1;
            foreach ($detailClientJoinedProgram as $client) {
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

            Log::error('Failed to get detail of success program dashboard data ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get detail of success program'
            ], 500);
        }

        return response()->json(
            [
                'success' => true,
                'ctx' => $html
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

            Log::error('Failed to get admission program dashboard data ' . $e->getMessage());
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
            'startDate' => $cp_filter['qdate'] . '-01',
            'endDate' => $cp_filter['qdate'] . '-31'
        ];
        try {

            $admissionsMentoring = $this->clientProgramRepository->getClientProgramGroupByStatusAndUserArray(['program' => 'Admissions Mentoring'] + $cp_filter);
            $initialConsultation = $this->clientProgramRepository->getInitialConsultationInformation($cp_filter);
            $totalInitialConsultation = array_sum($initialConsultation);
            $successProgram = $admissionsMentoring[2];

            $initialAssessmentMaking = $this->clientProgramRepository->getInitialMaking($dateDetails, $cp_filter);
            $conversionTimeProgress = $this->clientProgramRepository->getConversionTimeProgress($dateDetails, $cp_filter);
            $successPercentage = $successProgram == 0 || $totalInitialConsultation == 0 ? 0 : ($successProgram / $totalInitialConsultation) * 100;
            $totalRevenueAdmMentoringByProgramAndMonth = $this->clientProgramRepository->getTotalRevenueByProgramAndMonth(['program' => 'Admissions Mentoring'] + $cp_filter);
        } catch (Exception $e) {

            Log::error('Failed to get initial consultation dashboard data ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get initial consult data'
            ], 500);
        }

        # declare new variable
        $initial_assessment_making = isset($initialAssessmentMaking) ? (int) $initialAssessmentMaking->initialMaking : 0;
        $conversion_time = isset($conversionTimeProgress) ? (int) $conversionTimeProgress->conversionTime : 0;


        return response()->json(
            [
                'success' => true,
                'data' => [
                    'ctx' => $initialConsultation,
                    'details' => [
                        $totalInitialConsultation,
                        $successProgram,
                        $initial_assessment_making . ' Days',
                        $conversion_time . ' Days',
                        round($successPercentage) . ' %',
                        'Rp. ' . number_format($totalRevenueAdmMentoringByProgramAndMonth, '2', ',', '.')
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

            Log::error('Failed to get academic prep dashboard data ' . $e->getMessage());
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
                    'total_revenue' => 'Rp. ' . number_format($totalRevenueAcadTestPrepByMonth, '2', ',', '.')
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

            Log::error('Failed to get career exploration dashboard data ' . $e->getMessage());
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
                    'total_revenue' => 'Rp. ' . number_format($totalRevenueCareerExplorationByMonth, '2', ',', '.')
                ]
            ]
        );
    }

    public function getClientProgramByMonthDetail(Request $request)
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
                $type = 'Career Exploration';
                break;
            case 'admissions-mentoring':
                $type = 'Admissions Mentoring';
                break;
        }

        try {

            $clientProg = $this->clientProgramRepository->getClientProgramGroupDataByStatusAndUserArray(['program' => $type] + $cp_filter);
            // return $clientProg;

            $html = $table_content = null;
            foreach ($clientProg as $title => $data) {

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

            Log::error($e->getMessage() . ' | Line ' . $e->getLine());
            return response()->json(['message' => 'Failed to get detail ' . $type . 'detail : ' . $e->getMessage() . ' | Line ' . $e->getLine()]);
        }

        return response()->json([
            'success' => true,
            'data' => $clientProg,
            'ctx' => $html
        ]);
    }

    public function getConversionLeadByMonth(Request $request)
    {
        $dataset_leadsource_labels = $dataset_leadsource = $dataset_leadsource_bgcolor = $dataset_conversionlead_labels = $dataset_conversionlead = $dataset_conversionlead_bgcolor = [];
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;

        $dateDetails = [
            'startDate' => $cp_filter['qdate'] . '-01',
            'endDate' => $cp_filter['qdate'] . '-31'
        ];

        try {

            $leadSource = $this->clientProgramRepository->getLeadSource($dateDetails, $cp_filter);
            foreach ($leadSource as $source) {
                $dataset_leadsource_labels[] = $source->lead_source;
                $dataset_leadsource[] = $source->lead_source_count;
                $dataset_leadsource_bgcolor[] = $source->color_code;
            }

            $conversionLeads = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter);
            foreach ($conversionLeads as $source) {
                $dataset_conversionlead_labels[] = $source->conversion_lead;
                $dataset_conversionlead[] = $source->conversion_lead_count;
                $dataset_conversionlead_bgcolor[] = $source->color_code;
            }
        } catch (Exception $e) {

            Log::error('Failed to get conversion lead dashboard data ' . $e->getMessage());
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

    public function getLeadAdmissionsProgramByMonth(Request $request)
    {
        $dataset_lead_labels = $dataset_lead = $dataset_bgcolor = [];
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;
        $cp_filter['prog'] = 'Admissions Mentoring';

        $dateDetails = [
            'startDate' => $cp_filter['qdate'] . '-01',
            'endDate' => $cp_filter['qdate'] . '-31'
        ];

        try {

            $adminssionMentoringConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter);
            foreach ($adminssionMentoringConvLead as $source) {

                $dataset_lead_labels[] = $source->conversion_lead;
                $dataset_lead[] = $source->conversion_lead_count;
                $dataset_bgcolor[] = $source->color_code;
            }
        } catch (Exception $e) {

            Log::error('Failed to get admission mentoring lead dashboard data ' . $e->getMessage());
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
                        'bgcolor' => $dataset_bgcolor
                    ]
                ]
            ]
        );
    }

    public function getLeadAcademicPrepByMonth(Request $request)
    {
        $dataset_lead_labels = $dataset_lead = $dataset_bgcolor = [];
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;
        $cp_filter['prog'] = 'Academic & Test Preparation';

        $dateDetails = [
            'startDate' => $cp_filter['qdate'] . '-01',
            'endDate' => $cp_filter['qdate'] . '-31'
        ];

        try {

            $academicTestPrepConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter);
            foreach ($academicTestPrepConvLead as $source) {

                $dataset_lead_labels[] = $source->conversion_lead;
                $dataset_lead[] = $source->conversion_lead_count;
                $dataset_bgcolor[] = $source->color_code;
            }
        } catch (Exception $e) {

            Log::error('Failed to get academic prep lead dashboard data ' . $e->getMessage());
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
                        'bgcolor' => $dataset_bgcolor
                    ]
                ]
            ]
        );
    }

    public function getLeadCareerExplorationByMonth(Request $request)
    {
        $dataset_lead_labels = $dataset_lead = $dataset_bgcolor = [];
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;
        $cp_filter['prog'] = 'Experiential Learning'; # new

        $dateDetails = [
            'startDate' => $cp_filter['qdate'] . '-01',
            'endDate' => $cp_filter['qdate'] . '-31'
        ];

        try {

            $careerExplorationConvLead = $this->clientProgramRepository->getConversionLead($dateDetails, $cp_filter);
            foreach ($careerExplorationConvLead as $source) {

                $dataset_lead_labels[] = $source->conversion_lead;
                $dataset_lead[] = $source->conversion_lead_count;
                $dataset_bgcolor[] = $source->color_code;
            }
        } catch (Exception $e) {

            Log::error('Failed to get experiential learning lead dashboard data ' . $e->getMessage());
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
                        'bgcolor' => $dataset_bgcolor
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
            $percentage_participant = $detail->total_target_participant != 0 ? round(($detail->total_actual_participant / $detail->total_target_participant) * 100, 2) : 0;
            $percentage_revenue = $detail->total_target != 0 ? ($detail->total_actual_amount / $detail->total_target) * 100 : 0;

            $target_student = $detail->total_target_participant ??= 0;

            $html .= '<tr class="text-center">
                    <td>' . $no++ . '</td>
                    <td>' . (isset($detail->prog_id) ? $detail->prog_id : '-') . '</td>
                    <td class="text-start">' . $detail->program_name_sales . '</td>
                    <td>' . $target_student . '</td>
                    <td>' . number_format($detail->total_target, '2', ',', '.') . '</td>
                    <td>' . $detail->total_actual_participant . '</td>
                    <td>' . number_format($detail->total_actual_amount, '2', ',', '.') . '</td>
                    <td>' . $percentage_participant . '%</td>
                    <td>' . $percentage_revenue . '%</td>
                </tr>';
        }

        $html .= '<tr class="text-center">
                    <th colspan="3">Total</th>
                    <td><b>' . $salesDetail->sum('total_target_participant') . '</b></td>
                    <td><b>' . number_format($salesDetail->sum('total_target'), '2', ',', '.') . '</b></td>
                    <td><b>' . $salesDetail->sum('total_actual_participant') . '</b></td>
                    <td><b>' . number_format($salesDetail->sum('total_actual_amount'), '2', ',', '.') . '</b></td>
                    <td><b>' . ($salesDetail->sum('total_target_participant') != 0 ?  round(($salesDetail->sum('total_actual_participant') / $salesDetail->sum('total_target_participant')) * 100, 2) : 0) . '%</b></td>
                    <td><b>' . ($salesDetail->sum('total_target') != 0 ? ($salesDetail->sum('total_actual_amount') / $salesDetail->sum('total_target')) * 100 : 0) . '%</b></td>
                </tr>';

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
            'queryParams_year1' => $queries['first_year'],
            'queryParams_year2' => $queries['second_year'],
            'queryParams_monthyear1' => $queries['first_monthyear'],
            'queryParams_monthyear2' => $queries['second_monthyear'],
            'quuid' => $queries['uuid'] == 'all' ? null : $queries['uuid'],
        ];

        try {

            $comparisons = $this->clientProgramRepository->getComparisonBetweenYears($cp_filter);
            
        } catch (Exception $e) {

            Log::error('Failed to get comparasion program on sales dashboard. Error : '.$e->getMessage().' on line '.$e->getLine());
            return response()->json(['success' => false, 'data' => null]);
        }

        return response()->json(['success' => true, 'data' => $comparisons]);
    }

    public function getConversionLeadsByEventId(Request $request)
    {
        $eventId = $request->route('event');
    }

    public function exportClient()
    {
        return Excel::download(new DataClient(), 'data-client.xlsx');
    }

    public function getDetailInitialConsultByMonth(Request $request)
    {
        $cp_filter['qdate'] = $request->route('month');
        $cp_filter['quuid'] = $request->route('user') ?? null;
        $cp_filter['count'] = false;

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

        return response()->json([
            'success' => true,
            'data' => [
                'ctx' => $html
            ] 
        ]);

    }

}

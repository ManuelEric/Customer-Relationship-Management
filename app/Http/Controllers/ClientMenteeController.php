<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\InitialProgramRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Services\Master\ProgramService;
use Illuminate\Http\Request;

class ClientMenteeController extends Controller
{
    
    private ClientRepositoryInterface $clientRepository;
    private InitialProgramRepositoryInterface $initialProgramRepository;
    private ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;
    private ProgramRepositoryInterface $programRepository;
    private ReasonRepositoryInterface $reasonRepository;
    private UserRepositoryInterface $userRepository;
    private ProgramService $programService;


    public function __construct(ClientRepositoryInterface $clientRepository, InitialProgramRepositoryInterface $initialProgramRepository, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository, ReasonRepositoryInterface $reasonRepository, ProgramRepositoryInterface $programRepository, UserRepositoryInterface $userRepository, ProgramService $programService)
    {
        $this->clientRepository = $clientRepository;
        $this->initialProgramRepository = $initialProgramRepository;
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
        $this->reasonRepository = $reasonRepository;
        $this->programRepository = $programRepository;
        $this->userRepository = $userRepository;
        $this->programService = $programService;

    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->get('st');
            $school_name = $request->get('school_name');
            $graduation_year = $request->get('graduation_year');
            $as_datatables = true;
            $group_by = false;

            # array for advanced filter request
            $advanced_filter = [
                'school_name' => $school_name,
                'graduation_year' => $graduation_year,
            ];

            switch ($status) {

                case "mentee":
                    $model = $this->clientRepository->getAlumniMentees($group_by, $as_datatables, null, $advanced_filter);
                    break;

                case "non-mentee":
                    $model = $this->clientRepository->getAlumniNonMentees($group_by, $as_datatables, null, $advanced_filter);
                    break;
            }
            return $this->clientRepository->getDataTables($model);
        }

        $entries = app('App\Services\ClientStudentService')->advancedFilterClient();

        return view('pages.client.student.index-mentee')->with($entries);
    }

    public function show(Request $request)
    {
        $segment = $request->segment(3);
        $alumni_type = str_replace('-', '_', $segment);

        $mentee_id = $request->route($alumni_type);
        $student = $this->clientRepository->getClientById($mentee_id);
        $view_student = $this->clientRepository->getViewClientById($mentee_id);

        $programs = $this->programService->snGetProgramsB2c();

        $initial_programs = $this->initialProgramRepository->getAllInitProg();
        $history_leads = $this->clientLeadTrackingRepository->getHistoryClientLead($mentee_id);

        if (!$student)
            abort(404);

        $pic_active = null;
        if (count($student->picClient) > 0) {
            $pic_active = $student->picClient->where('status', 1)->first();
        }

        $sales_teams = $this->userRepository->rnGetAllUsersByDepartmentAndRole('Employee', 'Client Management');

        return view('pages.client.student.view')->with(
            [
                'student' => $student,
                'viewStudent' => $view_student,
                'initialPrograms' => $initial_programs,
                'historyLeads' => $history_leads,
                'programs' => $programs,
                'picActive' => $pic_active,
                'salesTeams' => $sales_teams,
            ]
        );
    }
}

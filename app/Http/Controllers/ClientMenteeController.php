<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\InitialProgramRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

class ClientMenteeController extends Controller
{
    
    private ClientRepositoryInterface $clientRepository;
    private InitialProgramRepositoryInterface $initialProgramRepository;
    private ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;
    private ProgramRepositoryInterface $programRepository;
    private ReasonRepositoryInterface $reasonRepository;
    private UserRepositoryInterface $userRepository;


    public function __construct(ClientRepositoryInterface $clientRepository, InitialProgramRepositoryInterface $initialProgramRepository, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository, ReasonRepositoryInterface $reasonRepository, ProgramRepositoryInterface $programRepository, UserRepositoryInterface $userRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->initialProgramRepository = $initialProgramRepository;
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
        $this->reasonRepository = $reasonRepository;
        $this->programRepository = $programRepository;
        $this->userRepository = $userRepository;

    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->get('st');
            $school_name = $request->get('school_name');
            $graduation_year = $request->get('graduation_year');
            $asDatatables = true;
            $groupBy = false;

            # array for advanced filter request
            $advanced_filter = [
                'school_name' => $school_name,
                'graduation_year' => $graduation_year,
            ];

            switch ($status) {

                case "mentee":
                    $model = $this->clientRepository->getAlumniMentees($groupBy, $asDatatables, null, $advanced_filter);
                    break;

                case "non-mentee":
                    $model = $this->clientRepository->getAlumniNonMentees($groupBy, $asDatatables, null, $advanced_filter);
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

        $menteeId = $request->route($alumni_type);
        $student = $this->clientRepository->getClientById($menteeId);
        $viewStudent = $this->clientRepository->getViewClientById($menteeId);

        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C', true);
        $programsB2C = $this->programRepository->getAllProgramByType('B2C', true);
        $programs = $programsB2BB2C->merge($programsB2C)->sortBy('program_name');

        $initialPrograms = $this->initialProgramRepository->getAllInitProg();
        $historyLeads = $this->clientLeadTrackingRepository->getHistoryClientLead($menteeId);

        if (!$student)
            abort(404);

        $picActive = null;
        if (count($student->picClient) > 0) {
            $picActive = $student->picClient->where('status', 1)->first();
        }

        $salesTeams = $this->userRepository->getAllUsersByDepartmentAndRole('Employee', 'Client Management');

        return view('pages.client.student.view')->with(
            [
                'student' => $student,
                'viewStudent' => $viewStudent,
                'initialPrograms' => $initialPrograms,
                'historyLeads' => $historyLeads,
                'programs' => $programs,
                'picActive' => $picActive,
                'salesTeams' => $salesTeams,
            ]
        );
    }
}

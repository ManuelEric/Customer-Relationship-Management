<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\InitialProgramRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use Illuminate\Http\Request;

class ClientMenteeController extends Controller
{
    
    private ClientRepositoryInterface $clientRepository;
    private InitialProgramRepositoryInterface $initialProgramRepository;
    private ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;
    private ReasonRepositoryInterface $reasonRepository;


    public function __construct(ClientRepositoryInterface $clientRepository, InitialProgramRepositoryInterface $initialProgramRepository, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository, ReasonRepositoryInterface $reasonRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->initialProgramRepository = $initialProgramRepository;
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
        $this->reasonRepository = $reasonRepository;

    }
    
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->get('st');
            $asDatatables = true;
            $groupBy = false;
            switch ($status) {

                case "mentee":
                    $model = $this->clientRepository->getAlumniMentees($groupBy, $asDatatables);
                    break;

                case "non-mentee":
                    $model = $this->clientRepository->getAlumniNonMentees($groupBy, $asDatatables);
                    break;
                 
            }
            return $this->clientRepository->getDataTables($model);
        }

        return view('pages.client.student.index-mentee');
    }

    public function show(Request $request)
    {
        $menteeId = $request->route('mentee');
        $student = $this->clientRepository->getClientById($menteeId);
        $viewStudent = $this->clientRepository->getViewClientById($menteeId);

        $initialPrograms = $this->initialProgramRepository->getAllInitProg();
        $historyLeads = $this->clientLeadTrackingRepository->getHistoryClientLead($menteeId);

        if (!$student)
            abort(404);

        return view('pages.client.student.view')->with(
            [
                'student' => $student,
                'viewStudent' => $viewStudent,
                'initialPrograms' => $initialPrograms,
                'historyLeads' => $historyLeads,
            ]
        );
    }
}

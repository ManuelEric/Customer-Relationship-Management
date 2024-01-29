<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\v1\DigitalDashboardController;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\LeadTargetRepositoryInterface;
use App\Interfaces\MainProgRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class SalesTrackingController extends Controller
{
    
    protected ClientProgramRepositoryInterface $clientProgramRepository;
    protected MainProgRepositoryInterface $mainProgRepository;
    protected UserRepositoryInterface $userRepository;
    protected ClientRepositoryInterface $clientRepository;
    protected LeadTargetRepositoryInterface $leadTargetRepository;

    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository, MainProgRepositoryInterface $mainProgRepository, UserRepositoryInterface $userRepository, ClientRepositoryInterface $clientRepository, LeadTargetRepositoryInterface $leadTargetRepository)
    {
        $this->clientProgramRepository = $clientProgramRepository;
        $this->mainProgRepository = $mainProgRepository;
        $this->userRepository = $userRepository;
        $this->clientRepository = $clientRepository;
        $this->leadTargetRepository = $leadTargetRepository;
    }

    public function index(Request $request)
    {
        # initialize
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        if ($request->get('start') && $request->get('end')) {
            $startDate = $request->get('start');
            $endDate = $request->get('end');
        }

        $mainProg = $request->get('main') ?? null; # main_prog
        $progName = $request->get('program') ?? null; # prog_id
        $picUUID = $request->get('pic') ?? null; # will be filled with employee uuid
        $picId = $this->userRepository->getUserByUUID($picUUID)->id ?? null;

        $dateDetails = [
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        $additionalFilter = [
            'mainProg' => $mainProg,
            'progName' => $progName,
            'pic' => $picId
        ];

        # processing the variables
        $countClientProgram = [
            'pending' => $this->clientProgramRepository->getCountProgramByStatus('Pending', $dateDetails, $additionalFilter),
            'failed' => $this->clientProgramRepository->getCountProgramByStatus('Failed', $dateDetails, $additionalFilter),
            'refund' => $this->clientProgramRepository->getCountProgramByStatus('Refund', $dateDetails, $additionalFilter),
            'success' => $this->clientProgramRepository->getCountProgramByStatus('Success', $dateDetails, $additionalFilter)
        ];

        $clientProgramDetail = [
            'pending' => $this->clientProgramRepository->getSummaryProgramByStatus('Pending', $dateDetails, $additionalFilter),
            'failed' => $this->clientProgramRepository->getSummaryProgramByStatus('Failed', $dateDetails, $additionalFilter),
            'refund' => $this->clientProgramRepository->getSummaryProgramByStatus('Refund', $dateDetails, $additionalFilter),
            'success' => $this->clientProgramRepository->getSummaryProgramByStatus('Success', $dateDetails, $additionalFilter)
        ];

        $initAssessmentProgress = $this->clientProgramRepository->getInitAssessmentProgress($dateDetails, $additionalFilter);
        $leadSource = $this->clientProgramRepository->getLeadSource($dateDetails);
        $conversionLead = $this->clientProgramRepository->getConversionLead($dateDetails);
        $averageConversionSuccessful = $this->clientProgramRepository->getConversionTimeSuccessfulPrograms($dateDetails);
        $mainPrograms = $this->mainProgRepository->getAllMainProg();
        $pics = $this->userRepository->getPICs();

        return view('pages.report.sales-tracking.index')->with(
            [
                'countClientProgram' => $countClientProgram,
                'clientProgramDetail' => $clientProgramDetail,
                'initAssessmentProgress' => $initAssessmentProgress,
                'leadSource' => $leadSource,
                'dateDetails' => $dateDetails,
                'conversionLead' => $conversionLead,
                'averageConversionSuccessful' => $averageConversionSuccessful,
                'mainPrograms' => $mainPrograms,
                'pics' => $pics
            ]
        );
    }
}

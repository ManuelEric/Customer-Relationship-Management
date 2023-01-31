<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientProgramRepositoryInterface;
use Illuminate\Http\Request;

class SalesTrackingController extends Controller
{
    
    protected ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->clientProgramRepository = $clientProgramRepository;
    }

    public function index(Request $request)
    {
        $countClientProgram = [
            'pending' => $this->clientProgramRepository->getCountProgramByStatus('Pending'),
            'failed' => $this->clientProgramRepository->getCountProgramByStatus('Failed'),
            'refund' => $this->clientProgramRepository->getCountProgramByStatus('Refund'),
            'success' => $this->clientProgramRepository->getCountProgramByStatus('Success')
        ];

        $clientProgramDetail = [
            'pending' => $this->clientProgramRepository->getSummaryProgramByStatus('Pending'),
            'failed' => $this->clientProgramRepository->getSummaryProgramByStatus('Failed'),
            'refund' => $this->clientProgramRepository->getSummaryProgramByStatus('Refund'),
            'success' => $this->clientProgramRepository->getSummaryProgramByStatus('Success')
        ];

        $initAssessmentProgress = $this->clientProgramRepository->getInitAssessmentProgress();
        $leadSource = $this->clientProgramRepository->getLeadSource();
        $conversionLead = $this->clientProgramRepository->getConversionLead();
        
        return view('pages.report.sales-tracking.index')->with(
            [
                'countClientProgram' => $countClientProgram,
                'clientProgramDetail' => $clientProgramDetail,
                'initAssessmentProgress' => $initAssessmentProgress,
                'leadSource' => $leadSource,
                'conversionLead' => $conversionLead
            ]
        );
    }
}

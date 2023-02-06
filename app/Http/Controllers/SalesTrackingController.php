<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientProgramRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SalesTrackingController extends Controller
{
    
    protected ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->clientProgramRepository = $clientProgramRepository;
    }

    public function index(Request $request)
    {
        $startDate = date('Y-m').'-01';
        $endDate = date('Y-m').'-31';
        if ($request->get('start') && $request->get('end')) {
            $startDate = $request->get('start');
            $endDate = $request->get('end');
        }

        $dateDetails = [
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        $countClientProgram = [
            'pending' => $this->clientProgramRepository->getCountProgramByStatus('Pending', $dateDetails),
            'failed' => $this->clientProgramRepository->getCountProgramByStatus('Failed', $dateDetails),
            'refund' => $this->clientProgramRepository->getCountProgramByStatus('Refund', $dateDetails),
            'success' => $this->clientProgramRepository->getCountProgramByStatus('Success', $dateDetails)
        ];

        $clientProgramDetail = [
            'pending' => $this->clientProgramRepository->getSummaryProgramByStatus('Pending', $dateDetails),
            'failed' => $this->clientProgramRepository->getSummaryProgramByStatus('Failed', $dateDetails),
            'refund' => $this->clientProgramRepository->getSummaryProgramByStatus('Refund', $dateDetails),
            'success' => $this->clientProgramRepository->getSummaryProgramByStatus('Success', $dateDetails)
        ];

        $initAssessmentProgress = $this->clientProgramRepository->getInitAssessmentProgress($dateDetails);
        $leadSource = $this->clientProgramRepository->getLeadSource($dateDetails);
        $conversionLead = $this->clientProgramRepository->getConversionLead($dateDetails);
        $averageConversionSuccessful = $this->clientProgramRepository->getConversionTimeSuccessfulPrograms($dateDetails);
        
        return view('pages.report.sales-tracking.index')->with(
            [
                'countClientProgram' => $countClientProgram,
                'clientProgramDetail' => $clientProgramDetail,
                'initAssessmentProgress' => $initAssessmentProgress,
                'leadSource' => $leadSource,
                'conversionLead' => $conversionLead,
                'averageConversionSuccessful' => $averageConversionSuccessful
            ]
        );
    }
}

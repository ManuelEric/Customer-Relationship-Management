<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\ClientProgramRepositoryInterface;
use Illuminate\Http\Request;

class ExtSalesTrackingController extends Controller
{
    protected ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->clientProgramRepository = $clientProgramRepository;
    }

    public function getLeadSourceDetail(Request $request)
    {
        $params = $request->only('leadId', 'startDate', 'endDate');

        $leadSourceDetail = $this->clientProgramRepository->getLeadSourceDetails($params);
        return json_encode($leadSourceDetail);
        
    }
}

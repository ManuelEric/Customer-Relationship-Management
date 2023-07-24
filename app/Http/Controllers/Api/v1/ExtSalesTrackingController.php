<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\ClientProgramRepositoryInterface;
use Exception;
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
        $params = $request->only('leadId', 'leadName', 'startDate', 'endDate');

        if (!$leadSourceDetail = $this->clientProgramRepository->getLeadSourceDetails($params))
            return response()->json(['success' => false, 'data' => 'No data found.']);
        
        try {

            $html = '';
            $no = 1;
            foreach ($leadSourceDetail as $data) {
    
                $html .= '<tr>
                            <td>'.$no++.'.</td>
                            <td>'.$data->first_name.' '.$data->last_name.'</td>
                            <td>'.$data->prog_program.'</td>
                        </tr>';
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'data' => 'Something went wrong. Please try again or contact the administrator.']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                    'title' => $params['leadName'],
                    'context' => $html
                ]
        ]);
    }

    public function getConversionLeadDetail(Request $request)
    {

    }
}

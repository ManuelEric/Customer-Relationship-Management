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
        $params = $request->only('leadId', 'leadName', 'startDate', 'endDate', 'subLead', 'mainProgId', 'progId', 'picUUID');

        if (!$leadSourceDetail = $this->clientProgramRepository->getLeadSourceDetails($params))
            return response()->json(['success' => false, 'data' => 'No data found.']);
        
        try {

            $html = '<thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Program</th>
                        </tr>
                    </thead>';
            $no = 1;
            foreach ($leadSourceDetail as $data) {
    
                $html .= '<tr>
                            <td>'.$no++.'.</td>
                            <td>'.$data->first_name.' '.$data->last_name.'</td>
                            <td>'.$data->invoice_program_name.'</td>
                        </tr>';
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'data' => 'Something went wrong. Please try again or contact the administrator.']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                    'title' => 'Detail of Lead Source \''.$params['leadName'].'\'',
                    'context' => $html
                ]
        ]);
    }

    public function getConversionLeadDetail(Request $request)
    {
        $params = $request->only('leadId', 'leadName', 'startDate', 'endDate', 'subLead', 'mainProgId', 'progId', 'picUUID');

        if (!$conversionLeadDetail = $this->clientProgramRepository->getConversionLeadDetails($params))
            return response()->json(['success' => false, 'data' => 'No data found.']);
        
        try {

            $html = '<thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Program</th>
                            <th>Lead Source</th>
                        </tr>
                    </thead>';
            $no = 1;
            foreach ($conversionLeadDetail as $data) {
    
                $html .= '<tr>
                            <td>'.$no++.'.</td>
                            <td>'.$data->first_name.' '.$data->last_name.'</td>
                            <td>'.$data->invoice_program_name.'</td>
                            <td>'.$data->lead_source.'</td>
                        </tr>';
            }
        } catch (Exception $e) {
            return response()->json(['success' => false, 'data' => 'Something went wrong. Please try again or contact the administrator.']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                    'title' => 'Detail of Conversion Lead \''.$params['leadName'].'\'',
                    'context' => $html
                ]
        ]);
    }
}

<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;
use App\Http\Traits\Modules\GetClientStatusTrait;
use App\Interfaces\ClientRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DigitalDashboardController extends Controller
{
    use GetClientStatusTrait;

    public function __construct($repositories)
    {
        $this->clientRepository = $repositories->clientRepository;
        $this->userRepository = $repositories->userRepository;
        $this->clientProgramRepository = $repositories->clientProgramRepository;
        $this->salesTargetRepository = $repositories->salesTargetRepository;
        $this->clientLeadTrackingRepository = $repositories->clientLeadTrackingRepository;
        $this->targetTrackingRepository = $repositories->targetTrackingRepository;
        $this->targetSignalRepository = $repositories->targetSignalRepository;
        $this->eventRepository = $repositories->eventRepository;
        $this->leadTargetRepository = $repositories->leadTargetRepository;
        $this->leadRepository = $repositories->leadRepository;
        $this->programRepository = $repositories->programRepository;

    }

    public function get($request)
    {

        $fullDay = Carbon::now()->daysInMonth;
        $midOfMonth = floor($fullDay / 2);
        // $alarm = new Collection();

        $today = date('Y-m-d');
        $currMonth = date('m');
        
        # List Lead Source 
        $leads = $this->leadRepository->getAllLead();
        
        $dataLeadDigtal = $this->leadTargetRepository->getLeadDigital($today, $prog_id ?? null);
        // $dataLeadDigtalSource = $this->leadTargetRepository->getLeadDigital($today, false, $prog_id ?? null);
        // $dataConversionLead = $this->leadTargetRepository->getLeadDigital($today, true, $prog_id ?? null);

        // $mergeLeadSourceAndConversionLead = $dataConversionLead->merge($dataLeadDigtalSource);

        $programsDigital = $this->programRepository->getAllPrograms();

        $response = [
            'leadsDigital' => $this->mappingDataLead($leads, $dataLeadDigtal, 'Lead Source'),
            'leadsAllDepart' => $this->mappingDataLead($leads, $dataLeadDigtal, 'Conversion Lead'),
            'dataLead' => $dataLeadDigtal,
            'programsDigital' => $programsDigital,

        ];

        return $response;
    }

    private function mappingDataLead($leads, $dataLead, $type)
    {
        $data = new Collection();
        foreach ($leads as $lead) {
            if($type == 'Lead Source'){
                $count = $dataLead->where('client.lead_id', $lead->lead_id)->count();
            }else if($type == 'Conversion Lead'){
                $count = $dataLead->where('lead_id', $lead->lead_id)->count();
            }
            
            if($count > 0){
                $data->push([
                    'lead_id' => $lead->lead_id,
                    'lead_name' => $lead->main_lead . ($lead->sub_lead  != null ? ' - ' . $lead->sub_lead : ''),
                    'count' => $count,

                ]);
            }
        }

        return $data;
    }

    
}

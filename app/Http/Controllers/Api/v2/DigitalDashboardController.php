<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Traits\Modules\GetClientStatusTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\SalesTargetRepositoryInterface;
use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Interfaces\TargetSignalRepositoryInterface;
use App\Interfaces\TargetTrackingRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Interfaces\LeadTargetRepositoryInterface;
use App\Interfaces\AlarmRepositoryInterface;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

class DigitalDashboardController extends Controller
{
    use GetClientStatusTrait;
    protected ClientRepositoryInterface $clientRepository;
    protected UserRepositoryInterface $userRepository;
    protected ClientProgramRepositoryInterface $clientProgramRepository;
    protected SalesTargetRepositoryInterface $salesTargetRepository;
    protected ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;
    protected TargetTrackingRepositoryInterface $targetTrackingRepository;
    protected TargetSignalRepositoryInterface $targetSignalRepository;
    protected EventRepositoryInterface $eventRepository;
    protected LeadTargetRepositoryInterface $leadTargetRepository;
    protected LeadRepositoryInterface $leadRepository;
    protected AlarmRepositoryInterface $alarmRepository;


    public function __construct(ClientRepositoryInterface $clientRepository, UserRepositoryInterface $userRepository, ClientProgramRepositoryInterface $clientProgramRepository, SalesTargetRepositoryInterface $salesTargetRepository, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository, TargetTrackingRepositoryInterface $targetTrackingRepository, TargetSignalRepositoryInterface $targetSignalRepository, EventRepositoryInterface $eventRepository, LeadTargetRepositoryInterface $leadTargetRepository, LeadRepositoryInterface $leadRepository, AlarmRepositoryInterface $alarmRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
        $this->clientProgramRepository = $clientProgramRepository;
        $this->salesTargetRepository = $salesTargetRepository;
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
        $this->targetTrackingRepository = $targetTrackingRepository;
        $this->targetSignalRepository = $targetSignalRepository;
        $this->eventRepository = $eventRepository;
        $this->leadTargetRepository = $leadTargetRepository;
        $this->leadRepository = $leadRepository;
        $this->alarmRepository = $alarmRepository;
    }

    public function getDataLead(Request $request)
    {
        $monthYear = $request->get('month') ?? date('Y-m');

        try {
             // digital dashboard
            Artisan::call('update:target_tracking', ['date' => $monthYear . '-01']);

            $currMonth = date('m', strtotime($monthYear));
            $currYear = date('Y', strtotime($monthYear));
            $date = CarbonImmutable::create($currYear, $currMonth);
            $last2month = $date->subMonth(2)->Format('Y-m');
            
            $allTarget = $this->targetTrackingRepository->getAllTargetTrackingMonthly($monthYear);
            $dataSalesTarget = $this->alarmRepository->getDataTarget($monthYear, 'Sales');
            $dataReferralTarget = $this->alarmRepository->getDataTarget($monthYear, 'Referral');
            $dataDigitalTarget = $this->alarmRepository->getDataTarget($monthYear, 'Digital');

            # Event
            $events = $this->eventRepository->getEventByMonthyear($monthYear);

            # sales
            $actualLeadsSales = $this->alarmRepository->setDataActual($dataSalesTarget);
            $leadSalesTarget = $this->alarmRepository->setDataTarget($dataSalesTarget, $actualLeadsSales);
            

            # referral
            $actualLeadsReferral = $this->alarmRepository->setDataActual($dataReferralTarget);
            $leadReferralTarget = $this->alarmRepository->setDataTarget($dataReferralTarget, $actualLeadsReferral);

            # digital
            $actualLeadsDigital = $this->alarmRepository->setDataActual($dataDigitalTarget);
            $leadDigitalTarget = $this->alarmRepository->setDataTarget($dataDigitalTarget, $actualLeadsDigital);

            $actualLeadsSales['referral'] = $actualLeadsReferral['lead_needed'];

            $targetTrackingLead = $this->targetTrackingRepository->getTargetTrackingPeriod($last2month, $monthYear, 'lead');
            $targetTrackingRevenue = $this->targetTrackingRepository->getTargetTrackingPeriod($last2month, $monthYear, 'revenue');

            # Chart lead
            for ($i = 2; $i >= 0; $i--) {
                $dataLeadChart['target'][] = $targetTrackingLead->where('month', date('m', strtotime($date->subMonth($i))))->count() > 0 ? (int)$targetTrackingLead->where('month', date('m', strtotime($date->subMonth($i))))->first()->target : 0;
                $dataLeadChart['actual'][] = $targetTrackingLead->where('month', date('m', strtotime($date->subMonth($i))))->count() > 0 ? (int)$targetTrackingLead->where('month', date('m', strtotime($date->subMonth($i))))->first()->actual : 0;
                $dataLeadChart['label'][] = date('F', strtotime($date->subMonth($i)));

                $dataRevenueChart['target'][] = $targetTrackingRevenue->where('month', date('m', strtotime($date->subMonth($i))))->count() > 0 ? (int)$targetTrackingRevenue->where('month', date('m', strtotime($date->subMonth($i))))->first()->target : 0;
                $dataRevenueChart['actual'][] = $targetTrackingRevenue->where('month', date('m', strtotime($date->subMonth($i))))->count() > 0 ? (int)$targetTrackingRevenue->where('month', date('m', strtotime($date->subMonth($i))))->first()->actual : 0;
                $dataRevenueChart['label'][] = date('F', strtotime($date->subMonth($i)));
            }


            $dataLeads = [
                'total_achieved_lead_needed' => $actualLeadsSales['lead_needed'] + $actualLeadsReferral['lead_needed'] + $actualLeadsDigital['lead_needed'],
                'total_achieved_hot_lead' => $actualLeadsSales['hot_lead'] + $actualLeadsReferral['hot_lead'] + $actualLeadsDigital['hot_lead'],
                'total_achieved_ic' => $actualLeadsSales['ic'] + $actualLeadsReferral['ic'] + $actualLeadsDigital['ic'],
                'total_achieved_contribution' => $actualLeadsSales['contribution'] + $actualLeadsReferral['contribution'] + $actualLeadsDigital['contribution'],
                'number_of_leads' => isset($allTarget) ? $allTarget->sum('target_lead') : 0,
                'number_of_hot_leads' => isset($allTarget) ? $allTarget->sum('target_hotleads') : 0,
                'number_of_ic' => isset($allTarget) ? $allTarget->sum('target_initconsult') : 0,
                'number_of_contribution' => isset($allTarget) ? $allTarget->sum('contribution_target') : 0,
            ];

        } catch (Exception $e) {
            Log::error('Failed to get number of leads ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get number of leads'
            ], 500);
        }
       
        $response = [
            'leadSalesTarget' => $leadSalesTarget,
            'leadReferralTarget' => $leadReferralTarget,
            'leadDigitalTarget' => $leadDigitalTarget,
            'actualLeadsDigital' => $actualLeadsDigital,
            'potentialLeadDigital' => null,
            'actualLeadsSales' => $actualLeadsSales,
            'actualLeadsReferral' => $actualLeadsReferral,
            'dataLeads' => $dataLeads,
            'dataLeadChart' => $dataLeadChart,
            'dataRevenueChart' => $dataRevenueChart,
            'monthYear' => $monthYear,
        ];

        return response()->json(
            [
                'success' => true,
                'data' => $response
            ]
        );
    }

    public function getLeadDigital(Request $request)
    {
        $month = $request->get('month') ?? date('Y-m');
        $prog_id = $request->get('prog') ?? null;


        try {
            # List Lead Source 
            $leads = $this->leadRepository->getAllLead();
            $dataLead = $this->leadTargetRepository->getLeadDigital($month, $prog_id ?? null);

            $response = [
                'leadsDigital' => $this->mappingDataLead($leads->where('department_id', 7), $dataLead, 'Lead Source'),
                'leadsAllDepart' => $this->mappingDataLead($leads, $dataLead, 'Conversion Lead'),
                'dataLead' => $dataLead,
            ];
            
        } catch (Exception $e) {
            Log::error('Failed to get lead digital ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get lead digital'
            ], 500);
        }

        return response()->json(
            [
                'success' => true,
                'data' => $response
            ]
        );

    }

    public function getDetailDataLead(Request $request)
    {
        $month = $request->get('month') ?? date('Y-m');
        $division = $request->get('division');
        $typeLead = $request->get('type_lead');
        $methodName = 'getAchieved' . $typeLead . $division . 'ByMonth';
        $html = '';

        try {
            $dataAchieved = $this->leadTargetRepository->{$methodName}($month);

            $data = [];
            foreach ($dataAchieved as $achieved) {
                
                $data[] = [
                    'client_id' => $achieved->id,
                    'full_name' => $achieved->full_name,
                    'parents_name' => (count($achieved->parents) > 0 ? $achieved->parents->first()->full_name : '-'),
                    'school_name' => (isset($achieved->school) ? $achieved->school->sch_name : '-'),
                    'graduation_year' => $achieved->graduation_year_real,
                    'lead_source' => $achieved->leadSource
                ];
            }
    
        } catch (Exception $e) {
            Log::error('Failed to get detail data lead ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get detail data lead'
            ], 500);
        }
      
        return response()->json(
            [
                'success' => true,
                'data' => $data
            ]
        );

        

    }

    public function getDetailLeadSource(Request $request)
    {
        $month = $request->get('month') ?? date('Y-m');
        $lead_id = $request->get('lead');
        $prog_id = $request->get('prog') ?? null;

        try {
            $dataLeadSource = $this->leadTargetRepository->getLeadDigital($month, $prog_id)->where('client.lead_id', $lead_id);

            $result = [];
            foreach ($dataLeadSource as $data) {

                $result[] = [
                    'full_name' => $data->client->full_name,
                    'parent_name' => (count($data->client->parents) > 0 ? $data->client->parents->first()->full_name : '-'),
                    'school_name' => (isset($data->client->school) ? $data->client->school->sch_name : '-'),
                    'graduation_year' => $data->client->graduation_year_real,
                    'lead_source' => (isset($data->client->lead_source) ? $data->client->lead_source : '-'),
                ];
            }
        } catch (Exception $e) {
            Log::error('Failed to get detail of leadsource ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get detail of leadsource'
            ], 500);
        }
        

        return response()->json(
            [
                'success' => true,
                'data' => $result
            ]
        );

    }

    public function getDetailConversionLead(Request $request)
    {
        $month = $request->get('month') ?? date('Y-m');
        $lead_id = $request->get('lead');
        $prog_id = $request->get('prog') ?? null;
        

        try {
            $dataConversionLead = $this->leadTargetRepository->getLeadDigital($month, $prog_id)->where('lead_id', $lead_id);

            $result = [];
            foreach ($dataConversionLead as $data) {
                    
                $result[] = [
                    'full_name' => $data->client->full_name,
                    'parent_name' => (count($data->client->parents) > 0 ? $data->client->parents->first()->full_name : '-'),
                    'school_name' => (isset($data->client->school) ? $data->client->school->sch_name : '-'),
                    'graduation_year' => $data->client->graduation_year_real,
                    'lead_source' => (isset($data->client->lead_source) ? $data->client->lead_source : '-'),
                    'conversion_lead' => (isset($data->lead_name) ? $data->lead_name : '-'),
                    'program_name' => $data->program->program_name,
                ];
            }
        } catch (Exception $e) {
            Log::error('Failed to get detail of conversion lead ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get detail of conversion lead'
            ], 500);
        }
       

        return response()->json(
            [
                'success' => true,
                'data' => $result
            ]
        );

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

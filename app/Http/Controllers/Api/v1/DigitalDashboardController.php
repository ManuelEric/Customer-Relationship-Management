<?php

namespace App\Http\Controllers\Api\v1;

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
        $monthYear = $request->route('month') ?? date('Y-m');
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



        $response = [
            'leadSalesTarget' => $leadSalesTarget,
            'leadReferralTarget' => $leadReferralTarget,
            'leadDigitalTarget' => $leadDigitalTarget,
            'actualLeadsDigital' => $actualLeadsDigital,
            'actualLeadsSales' => $actualLeadsSales,
            'actualLeadsReferral' => $actualLeadsReferral,
            'dataLeads' => $dataLeads,
            'dataLeadChart' => $dataLeadChart,
            'dataRevenueChart' => $dataRevenueChart,
            'monthYear' => $monthYear,
        ];

        return $response;
    }

    public function getLeadDigital(Request $request)
    {
        $month = $request->route('month') ?? date('Y-m');
        $prog_id = $request->route('prog') ?? null;

         
        # List Lead Source 
        $leads = $this->leadRepository->getAllLead();
        $dataLead = $this->leadTargetRepository->getLeadDigital($month, $prog_id ?? null);
        // $dataConversionLead = $this->leadTargetRepository->getConversionLeadDigital($today);


        $response = [
            'leadsDigital' => $this->mappingDataLead($leads->where('department_id', 7), $dataLead, 'Lead Source'),
            'leadsAllDepart' => $this->mappingDataLead($leads, $dataLead, 'Conversion Lead'),
            'dataLead' => $dataLead,
        ];

        return response()->json(
            [
                'success' => true,
                'data' => $response
            ]
        );

    }

    public function getDetailDataLead(Request $request,)
    {
        $month = $request->route('month') ?? date('Y-m');
        $division = $request->route('division');
        $typeLead = $request->route('type_lead');
        $methodName = 'getAchieved' . $typeLead . $division . 'ByMonth';
        $html = '';

        $dataAchieved = $this->leadTargetRepository->{$methodName}($month);
        if ($dataAchieved->count() == 0)
            return response()->json(['success' => true, 'html_ctx' => '<tr align="center"><td colspan="6">No data</td></tr>']);

        $index = 1;
        foreach ($dataAchieved as $achieved) {
            $achievedParents = $achieved->parents !== null ? $achieved->parents->count() : 0;
        
            $html .= '<tr>
                        <td>' . $index++ . '</td>
                        <td>' . $achieved->full_name . '</td>
                        <td>' . ($achievedParents > 0 ? $achieved->parents->first()->full_name : '-'). '</td>
                        <td>' . ($achieved->school != null ? $achieved->school->sch_name : '-') . '</td>
                        <td>' . $achieved->graduation_year_real . '</td>
                        <td>' . $achieved->leadSource . '</td>
                    </tr>';
        }

        return response()->json(
            [
                'success' => true,
                'html_ctx' => $html
            ]
        );

        

    }

    public function getDetailLeadSource(Request $request)
    {
        // $month = date('Y-m');
        $month = $request->route('month') ?? date('Y-m');
        $lead_id = $request->lead;
        $prog_id = $request->route('prog') ?? null;

        $dataLeadSource = $this->leadTargetRepository->getLeadDigital($month, $prog_id)->where('lead_source_id', $lead_id);

        $html = '';

        if ($dataLeadSource->count() == 0)
            return response()->json(['success' => true, 'html_ctx' => '<tr align="center"><td colspan="6">No data</td></tr>']);

        $index = 1;
        foreach ($dataLeadSource as $data) {
            $html .= '<tr>
                        <td>' . $index++ . '</td>
                        <td>' . $data->fullname . '</td>
                        <td>' . $data->parent_fullname . '</td>
                        <td>' . $data->school_name . '</td>
                        <td>' . $data->client->graduation_year_real . '</td>
                        <td>' . $data->lead_source . '</td>
                    </tr>';
        }

        return response()->json(
            [
                'success' => true,
                'html_ctx' => $html
            ]
        );

    }

    public function getDetailConversionLead(Request $request)
    {
        // $month = date('Y-m');
        $month = $request->route('month') ?? date('Y-m');
        $lead_id = $request->lead;
        $prog_id = $request->route('prog') ?? null;

        // $html = $lead_id;
        

        $dataConversionLead = $this->leadTargetRepository->getLeadDigital($month, $prog_id)->where('lead_id', $lead_id);

        $html = '';

        if ($dataConversionLead->count() == 0)
            return response()->json(['success' => true, 'html_ctx' => '<tr align="center"><td colspan="8">No data</td></tr>']);

        $index = 1;
        foreach ($dataConversionLead as $data) {
            $html .= '<tr>
                        <td>' . $index++ . '</td>
                        <td>' . $data->fullname . '</td>
                        <td>' . $data->parent_fullname . '</td>
                        <td>' . $data->school_name . '</td>
                        <td>' . $data->client->graduation_year_real . '</td>
                        <td>' . $data->lead_source . '</td>
                        <td>' . $data->conversion_lead . '</td>
                        <td>' . $data->program_name . '</td>
                    </tr>';
        }

        return response()->json(
            [
                'success' => true,
                'html_ctx' => $html
            ]
        );

    }

    private function mappingDataLead($leads, $dataLead, $type)
    {
        $data = new Collection();
        foreach ($leads as $lead) {
            if($type == 'Lead Source'){
                $count = $dataLead->where('lead_source_id', $lead->lead_id)->count();
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

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
use Carbon\Carbon;
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


    public function __construct(ClientRepositoryInterface $clientRepository, UserRepositoryInterface $userRepository, ClientProgramRepositoryInterface $clientProgramRepository, SalesTargetRepositoryInterface $salesTargetRepository, ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository, TargetTrackingRepositoryInterface $targetTrackingRepository, TargetSignalRepositoryInterface $targetSignalRepository, EventRepositoryInterface $eventRepository, LeadTargetRepositoryInterface $leadTargetRepository, LeadRepositoryInterface $leadRepository)
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
    }

    // public function getDataLead(Request $request)
    // {

    //     $month = $request->route('month') . '-01' ?? date('Y-m') . '-01';
    //     $curr_month = date('m', strtotime($month));
    //     $last_month = date('Y-m', strtotime('-1 month', strtotime($month)));
        

    //     try {

    //         $allTarget = $this->targetTrackingRepository->getAllTargetTrackingMonthly($month);
    //         $dataSalesTarget = $this->getDataTarget($month, 'Sales');
    //         $dataReferralTarget = $this->getDataTarget($month, 'Referral');
    //         $dataDigitalTarget = $this->getDataTarget($month, 'Digital');
            
    //         # sales
    //         $actualLeadsSales = $this->setDataActual($dataSalesTarget);
    //         $leadSalesTarget = $this->setDataTarget($dataSalesTarget, $actualLeadsSales); 

    //         # referral
    //         $actualLeadsReferral = $this->setDataActual($dataReferralTarget);
    //         $leadReferralTarget = $this->setDataTarget($dataReferralTarget, $actualLeadsReferral); 
            
    //         # digital
    //         $actualLeadsDigital = $this->setDataActual($dataDigitalTarget);
    //         $leadDigitalTarget = $this->setDataTarget($dataDigitalTarget, $actualLeadsDigital); 

    //         // $revenueTarget = $dataSalesTarget->total_target;
    //         $revenueTarget = 0;

    //         $revenue = null;
    //         $totalRevenue = $revenue != null ? $revenue->sum('total') : 0;

    //         $dataLeads = [
    //             'total_achieved_lead_needed' => $actualLeadsSales['lead_needed'] + $actualLeadsReferral['lead_needed'] + $actualLeadsDigital['lead_needed'],
    //             'total_achieved_hot_lead' => $actualLeadsSales['hot_lead'] + $actualLeadsReferral['hot_lead'] + $actualLeadsDigital['hot_lead'],
    //             'total_achieved_ic' => $actualLeadsSales['ic'] + $actualLeadsReferral['ic'] + $actualLeadsDigital['ic'],
    //             'total_achieved_contribution' => $actualLeadsSales['contribution'] + $actualLeadsReferral['contribution'] + $actualLeadsDigital['contribution'],
    //             'number_of_leads' => isset($allTarget) ? $allTarget->sum('target_lead') : 0, 
    //             'number_of_hot_leads' => isset($allTarget) ? $allTarget->sum('target_hotleads') : 0, 
    //             'number_of_ic' => isset($allTarget) ? $allTarget->sum('target_initconsult') : 0, 
    //             'number_of_contribution' => isset($allTarget) ? $allTarget->sum('contribution_target') : 0, 
    //         ];

    //         $targetTrackingLead = $this->targetTrackingRepository->getTargetTrackingPeriod(Carbon::now()->startOfMonth()->subMonth(2)->toDateString(), $month, 'lead');
    //         $targetTrackingRevenue = $this->targetTrackingRepository->getTargetTrackingPeriod(Carbon::now()->startOfMonth()->subMonth(2)->toDateString(), $month, 'revenue');
        
    //         # Chart lead
    //         $last3month = $curr_month-2;
    //         for($i=2; $i>=0; $i--){
    //             $dataLeadChart['target'][] = $targetTrackingLead->where('month', $last3month)->count() > 0 ? (int)$targetTrackingLead->where('month', $last3month)->first()->target : 0;
    //             $dataLeadChart['actual'][] = $targetTrackingLead->where('month', $last3month)->count() > 0 ? (int)$targetTrackingLead->where('month', $last3month)->first()->actual : 0;
    //             $dataLeadChart['label'][] =  Carbon::parse($month)->subMonth($i)->format('F');
                
    //             $dataRevenueChart['target'][] = $targetTrackingRevenue->where('month', $last3month)->count() > 0 ? (int)$targetTrackingRevenue->where('month', $last3month)->first()->target : 0;
    //             $dataRevenueChart['actual'][] = $targetTrackingRevenue->where('month', $last3month)->count() > 0 ? (int)$targetTrackingRevenue->where('month', $last3month)->first()->actual : 0;
    //             $dataRevenueChart['label'][] =  Carbon::parse($month)->subMonth($i)->format('F');
    //             $last3month++;
    //         }

    //         # List Lead Source
            
    //         $leads = $this->leadRepository->getActiveLead()->where('department_id', 7);
    //         $dataLeadSource = $this->leadTargetRepository->getAchievedLeadDigitalByMonth($month);
        
    //         $data = [
    //             'leadSalesTarget' => $leadSalesTarget,
    //             'leadReferralTarget' => $leadReferralTarget,
    //             'leadDigitalTarget' => $leadDigitalTarget,
    //             'actualLeadsDigital' => $actualLeadsDigital,
    //             'actualLeadsSales' => $actualLeadsSales,
    //             'actualLeadsReferral' => $actualLeadsReferral,
    //             'dataLeads' => $dataLeads,
    //             'dataLeadChart' => $dataLeadChart,
    //             'dataRevenueChart' => $dataRevenueChart,
    //             'leads' => $leads,
    //             'dataLeadSource' => $dataLeadSource,
    //         ];

    //     } catch (Exception $e) {

    //         Log::error($e->getMessage() . ' | Line ' . $e->getLine());
    //         return response()->json(['message' => 'Failed to get data lead : ' . $e->getMessage() . ' | Line ' . $e->getLine()]);
    //     }

    //     return response()->json(
    //         [
    //             'success' => true,
    //             'data' => $data
    //         ]
    //     );
    // }

    public function getDataLead(Request $request)
    {
        $month = $request->route('month') ?? date('Y-m');
        $prog_id = $request->route('prog') ?? null;

         
        # List Lead Source 
        $leads = $this->leadRepository->getActiveLead();
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

    public function getDetailDataLead(Request $request)
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
            $html .= '<tr>
                        <td>' . $index++ . '</td>
                        <td>' . $achieved->full_name . '</td>
                        <td>' . ($achieved->parents->count() > 0 ? $achieved->parents->first()->full_name : '-'). '</td>
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
        $month = date('Y-m');
        $lead_id = $request->lead;

        $dataLeadSource = $this->leadTargetRepository->getLeadDigital($month, null)->where('lead_source_id', $lead_id);

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
        $month = date('Y-m');
        $lead_id = $request->lead;

        // $html = $lead_id;
        

        $dataConversionLead = $this->leadTargetRepository->getLeadDigital($month, null)->where('lead_id', $lead_id);

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

            $data->push([
                'lead_id' => $lead->lead_id,
                'lead_name' => $lead->main_lead . ($lead->sub_lead  != null ? ' - ' . $lead->sub_lead : ''),
                'count' => $count,

            ]);
        }

        return $data;
    }

 
}

<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;
use App\Http\Traits\Modules\GetClientStatusTrait;
use App\Interfaces\ClientRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AlarmController extends Controller
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
    }

    public function get($request)
    {

        $salesAlarm = false;
        $triggerEvent = false;
        $fullDay = Carbon::now()->daysInMonth;
        $midOfMonth = floor($fullDay / 2);

        $today = date('Y-m-d');
        $currMonth = date('m');

        $allTarget = $this->targetSignalRepository->getAllTargetSignal();
        $dataSalesTarget = $this->targetTrackingRepository->getTargetTrackingMonthlyByDivisi($today, 'Sales');
        $dataReferralTarget = $this->targetTrackingRepository->getTargetTrackingMonthlyByDivisi($today, 'Referral');
        $dataDigitalTarget = $this->targetTrackingRepository->getTargetTrackingMonthlyByDivisi($today, 'Digital');
        
        # sales
        $leadSalesTarget = [
            'ic' => 0,
            'hot_lead' => 0,
            'lead_needed' => 0,
            'contribution' => 0,
            'percentage_lead_needed' => 0,
            'percentage_hot_lead' => 0,
            'percentage_ic' => 0,
            'percentage_contribution' => 0,
        ];
        if($dataSalesTarget->count() > 0){
            $leadSalesTarget = [
                'ic' => $dataSalesTarget->target_initconsult,
                'hot_lead' => $dataSalesTarget->target_hotleads,
                'lead_needed' => $dataSalesTarget->target_lead,
                'contribution' => $dataSalesTarget->contribution_target,
            ];
        }

        # referral
        $leadReferralTarget = [
            'ic' => 0,
            'hot_lead' => 0,
            'lead_needed' => 0,
            'contribution' => 0,
            'percentage_lead_needed' => 0,
            'percentage_hot_lead' => 0,
            'percentage_ic' => 0,
            'percentage_contribution' => 0,
        ];
        if($dataReferralTarget->count() > 0){
            $leadReferralTarget = [
                'ic' => $dataReferralTarget->target_initconsult,
                'hot_lead' => $dataReferralTarget->target_hotleads,
                'lead_needed' => $dataReferralTarget->target_lead,
                'contribution' => $dataReferralTarget->contribution_target,
            ];
        }

        # digital
        $leadDigitalTarget = [
            'ic' => 0,
            'hot_lead' => 0,
            'lead_needed' => 0,
            'contribution' => 0,
            'percentage_lead_needed' => 0,
            'percentage_hot_lead' => 0,
            'percentage_ic' => 0,
            'percentage_contribution' => 0,
        ];

        if($dataDigitalTarget->count() > 0){
            $leadDigitalTarget = [
                'ic' => $dataDigitalTarget->target_initconsult,
                'hot_lead' => $dataDigitalTarget->target_hotleads,
                'lead_needed' => $dataDigitalTarget->target_lead,
                'contribution' => $dataDigitalTarget->contribution_target,
            ];
        }

        // $revenueTarget = $dataSalesTarget->total_target;
        $revenueTarget = 0;

        $clientLeadSales = $dataSalesTarget;
        $clientLeadReferral = $dataReferralTarget;
        $clientLeadDigital = $dataDigitalTarget;

        # Gagal 3x berturut-turut
        $allAlarm['always_on'] = $this->clientLeadTrackingRepository->getFailedLead($today);

        $revenue = null;
        $totalRevenue = $revenue != null ? $revenue->sum('total') : 0;

        # sales
        $actualLeadsSales = [
            'lead_needed' => $clientLeadSales->count() > 0 ? $clientLeadSales->achieved_lead : 0,
            'hot_lead' => $clientLeadSales->count() > 0 ? $clientLeadSales->achieved_hotleads : 0,
            'IC' => $clientLeadSales->count() > 0 ? $clientLeadSales->achieved_initconsult : 0,
            'revenue' => $totalRevenue,
            'contribution' => $clientLeadSales->count() > 0 ? $clientLeadSales->contribution_achieved : 0,
        ];

        # referral
        $actualLeadsReferral = [
            'lead_needed' => $clientLeadReferral->count() > 0 ? $clientLeadReferral->achieved_lead : 0,
            'hot_lead' => $clientLeadReferral->count() > 0 ? $clientLeadReferral->achieved_hotleads : 0,
            'IC' => $clientLeadReferral->count() > 0 ? $clientLeadReferral->achieved_initconsult : 0,
            'revenue' => 0,
            'contribution' => $clientLeadReferral->count() > 0 ? $clientLeadReferral->contribution_achieved : 0,
        ];

        # digital
        $actualLeadsDigital = [
            'lead_needed' => $clientLeadDigital->count() > 0 ? $clientLeadDigital->achieved_lead : 0,
            'hot_lead' => $clientLeadDigital->count() > 0 ? $clientLeadDigital->achieved_hotleads : 0,
            'IC' => $clientLeadDigital->count() > 0 ? $clientLeadDigital->achieved_initconsult : 0,
            'revenue' => $totalRevenue,
            'contribution' => $clientLeadDigital->count() > 0 ? $clientLeadDigital->contribution_achieved : 0,
        ];

        # Day 1-14 (awal bulan)
        $salesAlarm['mid']['lead_needed'] = $actualLeadsSales['lead_needed'] < $leadSalesTarget['lead_needed'] ? true : false;
        $salesAlarm['mid']['hot_lead'] = $actualLeadsSales['hot_lead'] < $leadSalesTarget['hot_lead'] ? true : false;
        $salesAlarm['mid']['referral'] = $actualLeadsReferral['lead_needed'] < 10 ? true : false;
        $triggerEvent = $salesAlarm['mid']['hot_lead'] || $salesAlarm['mid']['referral'] ? true : false;
        $digitalAlarm['mid']['hot_lead'] = $actualLeadsDigital['hot_lead'] < (4*$leadDigitalTarget['hot_lead']) ? true : false;

        # Day 15-30 (akhir bulan)
        if (date('Y-m-d') > date('Y-m') . '-' . $midOfMonth) {
            # sales
            unset($salesAlarm['mid']['lead_needed']);
            $salesAlarm['end']['revenue'] = $actualLeadsSales['revenue'] < $revenueTarget*50/100 ? true : false;
            $salesAlarm['end']['IC'] = $actualLeadsSales['IC'] < $leadSalesTarget['IC'] ? true : false;
            $salesAlarm['end']['hot_lead'] = $actualLeadsSales['hot_lead'] < 2*$leadSalesTarget['hot_lead'] ? true : false;
            
            # digital
            unset($digitalAlarm['mid']['lead_needed']);
            $digitalAlarm['end']['hot_lead'] = $actualLeadsDigital['hot_lead'] < (4*$leadDigitalTarget['hot_leads']) ? true : false;
            $digitalAlarm['end']['lead_needed'] = $actualLeadsDigital['lead_needed'] < $leadDigitalTarget['lead_needed'] ? true : false;

        }

        $dataLeads = [
            'total_achieved_lead_needed' => $actualLeadsSales['lead_needed'] + $actualLeadsReferral['lead_needed'] + $actualLeadsDigital['lead_needed'],
            'total_achieved_hot_lead' => $actualLeadsSales['hot_lead'] + $actualLeadsReferral['hot_lead'] + $actualLeadsDigital['hot_lead'],
            'total_achieved_ic' => $actualLeadsSales['IC'] + $actualLeadsReferral['IC'] + $actualLeadsDigital['IC'],
            'total_achieved_contribution' => $actualLeadsSales['contribution'] + $actualLeadsReferral['contribution'] + $actualLeadsDigital['contribution'],
            'number_of_leads' => $allTarget->sum('lead_needed'), 
            'number_of_hot_leads' => $allTarget->sum('hot_leads_target'), 
            'number_of_ic' => $allTarget->sum('initial_consult_target'), 
            'number_of_contribution' => $allTarget->sum('contribution_to_target'), 
        ];

        $leadSalesTarget['percentage_lead_needed'] = $this->calculatePercentageLead($actualLeadsSales['lead_needed'], $leadSalesTarget['lead_needed']);
        $leadSalesTarget['percentage_hot_lead'] = $this->calculatePercentageLead($actualLeadsSales['hot_lead'], $leadSalesTarget['hot_lead']);
        $leadSalesTarget['percentage_ic'] = $this->calculatePercentageLead($actualLeadsSales['IC'], $leadSalesTarget['ic']);
        $leadSalesTarget['percentage_contribution'] = $this->calculatePercentageLead($actualLeadsSales['contribution'], $leadSalesTarget['contribution']);
        
        $leadReferralTarget['percentage_lead_needed'] = $this->calculatePercentageLead($actualLeadsReferral['lead_needed'], $leadReferralTarget['lead_needed']);
        $leadReferralTarget['percentage_hot_lead'] = $this->calculatePercentageLead($actualLeadsReferral['hot_lead'], $leadReferralTarget['hot_lead']);
        $leadReferralTarget['percentage_ic'] = $this->calculatePercentageLead($actualLeadsReferral['IC'], $leadReferralTarget['ic']);
        $leadReferralTarget['percentage_contribution'] = $this->calculatePercentageLead($actualLeadsReferral['contribution'], $leadReferralTarget['contribution']);
        
        $leadDigitalTarget['percentage_lead_needed'] = $actualLeadsDigital['lead_needed']/$leadDigitalTarget['lead_needed']*100;
        $leadDigitalTarget['percentage_hot_lead'] = $actualLeadsDigital['hot_lead']/$leadDigitalTarget['hot_lead']*100;
        $leadDigitalTarget['percentage_ic'] = $actualLeadsDigital['IC']/$leadDigitalTarget['ic']*100;
        $leadDigitalTarget['percentage_contribution'] = $actualLeadsDigital['contribution']/$leadDigitalTarget['contribution']*100;


        $targetTrackingPeriod = $this->targetTrackingRepository->getTargetTrackingPeriod(Carbon::now()->startOfMonth()->subMonth(2)->toDateString(), $today);
        
        # Chart lead
        $last3month = $currMonth-2;
        for($i=0; $i<3; $i++){
            $dataLeadChart['target'][] = $targetTrackingPeriod->where('month', $last3month)->count() > 0 ? (int)$targetTrackingPeriod->where('month', $last3month)->first()->target : 0;
            $dataLeadChart['actual'][] = $targetTrackingPeriod->where('month', $last3month)->count() > 0 ? (int)$targetTrackingPeriod->where('month', $last3month)->first()->actual : 0;
            $dataLeadChart['label'][] = Carbon::now()->startOfMonth()->subMonth($last3month)->format('F');
            $last3month++;
        }
      
        $response = [

            # alarm
            'salesAlarm' => $salesAlarm,
            'digitalAlarm' => $digitalAlarm,
            'allAlarm' => $allAlarm,
            'leadSalesTarget' => $leadSalesTarget,
            'leadReferralTarget' => $leadReferralTarget,
            'leadDigitalTarget' => $leadDigitalTarget,
            'actualLeadsDigital' => $actualLeadsDigital,
            'actualLeadsSales' => $actualLeadsSales,
            'actualLeadsReferral' => $actualLeadsReferral,
            'triggerEvent' => $triggerEvent,
            'dataLeads' => $dataLeads,
            'dataLeadChart' => $dataLeadChart
        ];

        return $response;
    }

    private function calculatePercentageLead($actual, $target)
    {
        if ($target == 0)
            return 0;

        return $actual/$target*100;
    }

}

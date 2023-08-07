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
        $this->eventRepository = $repositories->eventRepository;
    }

    public function get($request)
    {

        $salesAlarm = false;
        $allAlarm = false;
        $fullDay = Carbon::now()->daysInMonth;
        $midOfMonth = floor($fullDay / 2);

        $today = date('Y-m-d');
        $currMonth = date('m');

        $allTarget = $this->targetTrackingRepository->getAllTargetTrackingMonthly($today);
        $dataSalesTarget = $this->getDataTarget($today, 'Sales');
        $dataReferralTarget = $this->getDataTarget($today, 'Referral');
        $dataDigitalTarget = $this->getDataTarget($today, 'Digital');
        
        # Event
        $events = $this->eventRepository->getEventByMonthyear($today);

        # sales
        $actualLeadsSales = $this->setDataActual($dataSalesTarget);
        $leadSalesTarget = $this->setDataTarget($dataSalesTarget, $actualLeadsSales); 

        # referral
        $actualLeadsReferral = $this->setDataActual($dataReferralTarget);
        $leadReferralTarget = $this->setDataTarget($dataReferralTarget, $actualLeadsReferral); 
        
        # digital
        $actualLeadsDigital = $this->setDataActual($dataDigitalTarget);
        $leadDigitalTarget = $this->setDataTarget($dataDigitalTarget, $actualLeadsDigital); 

        // $revenueTarget = $dataSalesTarget->total_target;
        $revenueTarget = 0;

        # Gagal 3x berturut-turut
        $allAlarm['always_on'] = $this->clientLeadTrackingRepository->getFailedLead($today);

        $revenue = null;
        $totalRevenue = $revenue != null ? $revenue->sum('total') : 0;

        # Day 1-14 (awal bulan)
        $salesAlarm['mid']['lead_needed'] = $actualLeadsSales['lead_needed'] < $leadSalesTarget['lead_needed'] ? true : false;
        $salesAlarm['mid']['hot_lead'] = $actualLeadsSales['hot_lead'] < $leadSalesTarget['hot_lead'] ? true : false;
        $salesAlarm['mid']['referral'] = $actualLeadsReferral['lead_needed'] < 10 ? true : false;
        $digitalAlarm['mid']['hot_lead'] = $actualLeadsDigital['hot_lead'] < (4*$leadDigitalTarget['hot_lead']) ? true : false;
        $allAlarm['event'] = $salesAlarm['mid']['hot_lead'] || $salesAlarm['mid']['referral'] && $events->count() < 1 ? true : false;

        // return $allAlarm;
        // exit;
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
            'total_achieved_ic' => $actualLeadsSales['ic'] + $actualLeadsReferral['ic'] + $actualLeadsDigital['ic'],
            'total_achieved_contribution' => $actualLeadsSales['contribution'] + $actualLeadsReferral['contribution'] + $actualLeadsDigital['contribution'],
            'number_of_leads' => isset($allTarget) ? $allTarget->sum('target_lead') : 0, 
            'number_of_hot_leads' => isset($allTarget) ? $allTarget->sum('target_hotleads') : 0, 
            'number_of_ic' => isset($allTarget) ? $allTarget->sum('target_initconsult') : 0, 
            'number_of_contribution' => isset($allTarget) ? $allTarget->sum('contribution_target') : 0, 
        ];

        $targetTrackingPeriod = $this->targetTrackingRepository->getTargetTrackingPeriod(Carbon::now()->startOfMonth()->subMonth(2)->toDateString(), $today);
        
        # Chart lead
        $last3month = $currMonth-2;
        for($i=2; $i>=0; $i--){
            $dataLeadChart['target'][] = $targetTrackingPeriod->where('month', $last3month)->count() > 0 ? (int)$targetTrackingPeriod->where('month', $last3month)->first()->target : 0;
            $dataLeadChart['actual'][] = $targetTrackingPeriod->where('month', $last3month)->count() > 0 ? (int)$targetTrackingPeriod->where('month', $last3month)->first()->actual : 0;
            $dataLeadChart['label'][] = Carbon::now()->startOfMonth()->subMonth($i)->format('F');
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
            'dataLeads' => $dataLeads,
            'dataLeadChart' => $dataLeadChart
        ];

        return $response;
    }

    private function getDataTarget($date, $divisi)
    {
        return $this->targetTrackingRepository->getTargetTrackingMonthlyByDivisi($date, $divisi);
    }

    private function calculatePercentageLead($actual, $target)
    {
        if ($target == 0)
            return 0;

        return $actual/$target*100;
    }

    private function setDataActual($dataActual)
    {
        $data = [
            'lead_needed' => isset($dataActual) ? $dataActual->achieved_lead : 0,
            'hot_lead' => isset($dataActual) ? $dataActual->achieved_hotleads : 0,
            'ic' => isset($dataActual) ? $dataActual->achieved_initconsult : 0,
            'revenue' => 0,
            'contribution' => isset($dataActual) ? $dataActual->contribution_achieved : 0,
        ];

        return $data;
    }

    private function setDataTarget($dataTarget, $dataActual)
    {
        $data = [
            'ic' => 0,
            'hot_lead' => 0,
            'lead_needed' => 0,
            'contribution' => 0,
            'percentage_lead_needed' => 0,
            'percentage_hot_lead' => 0,
            'percentage_ic' => 0,
            'percentage_contribution' => 0,
        ];
        if(isset($dataTarget)){
            $data = [
                'ic' => $dataTarget->target_initconsult,
                'hot_lead' => $dataTarget->target_hotleads,
                'lead_needed' => $dataTarget->target_lead,
                'contribution' => $dataTarget->contribution_target,
                'percentage_lead_needed' => $this->calculatePercentageLead($dataActual['lead_needed'], $dataTarget->target_lead),
                'percentage_hot_lead' => $this->calculatePercentageLead($dataActual['hot_lead'], $dataTarget->target_hotleads),
                'percentage_ic' => $this->calculatePercentageLead($dataActual['ic'], $dataTarget->target_initconsult),
                'percentage_contribution' => $this->calculatePercentageLead($dataActual['contribution'], $dataTarget->contribution_target)
                
            ];
        }

        return $data;
    }
 
}

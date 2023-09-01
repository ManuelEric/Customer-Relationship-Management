<?php

namespace App\Repositories;

use App\Interfaces\AlarmRepositoryInterface;
use App\Interfaces\ClientLeadTrackingRepositoryInterface;
use App\Interfaces\EventRepositoryInterface;
use App\Interfaces\TargetTrackingRepositoryInterface;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AlarmRepository implements AlarmRepositoryInterface
{
    protected ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository;
    protected TargetTrackingRepositoryInterface $targetTrackingRepository;
    protected EventRepositoryInterface $eventRepository;

    public function __construct(ClientLeadTrackingRepositoryInterface $clientLeadTrackingRepository, TargetTrackingRepositoryInterface $targetTrackingRepository, EventRepositoryInterface $eventRepository)
    {
        $this->clientLeadTrackingRepository = $clientLeadTrackingRepository;
        $this->targetTrackingRepository = $targetTrackingRepository;
        $this->eventRepository = $eventRepository;
    }

    public function getDataTarget($date, $divisi)
    {
        return $this->targetTrackingRepository->getTargetTrackingMonthlyByDivisi($date, $divisi);
    }

    public function setDataTarget($dataTarget, $dataActual)
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
            'revenue' => 0,
        ];
        if (isset($dataTarget)) {
            $data = [
                'ic' => $dataTarget->target_initconsult,
                'hot_lead' => $dataTarget->target_hotleads,
                'lead_needed' => $dataTarget->target_lead,
                'contribution' => $dataTarget->contribution_target,
                'percentage_lead_needed' => $this->calculatePercentageLead($dataActual['lead_needed'], $dataTarget->target_lead),
                'percentage_hot_lead' => $this->calculatePercentageLead($dataActual['hot_lead'], $dataTarget->target_hotleads),
                'percentage_ic' => $this->calculatePercentageLead($dataActual['ic'], $dataTarget->target_initconsult),
                'percentage_contribution' => $this->calculatePercentageLead($dataActual['contribution'], $dataTarget->contribution_target),
                'revenue' =>  $dataTarget->revenue_target,
            ];
        }

        return $data;
    }

    public function setDataActual($dataActual)
    {
        $data = [
            'lead_needed' => isset($dataActual) ? $dataActual->achieved_lead : 0,
            'hot_lead' => isset($dataActual) ? $dataActual->achieved_hotleads : 0,
            'ic' => isset($dataActual) ? $dataActual->achieved_initconsult : 0,
            'contribution' => isset($dataActual) ? $dataActual->contribution_achieved : 0,
            'revenue' => isset($dataActual) ? $dataActual->revenue_achieved : 0,
        ];

        return $data;
    }

    public function setAlarmLead()
    {
        $fullDay = Carbon::now()->daysInMonth;
        $midOfMonth = floor($fullDay / 2);
        $today = date('Y-m-d');
        $currMonth = date('m');

        $dataSalesTarget = $this->getDataTarget($today, 'Sales');
        $dataReferralTarget = $this->getDataTarget($today, 'Referral');
        $dataDigitalTarget = $this->getDataTarget($today, 'Digital');

        # sales
        $actualLeadsSales = $this->setDataActual($dataSalesTarget);
        $leadSalesTarget = $this->setDataTarget($dataSalesTarget, $actualLeadsSales);
        
        # referral
        $actualLeadsReferral = $this->setDataActual($dataReferralTarget);
        $leadReferralTarget = $this->setDataTarget($dataReferralTarget, $actualLeadsReferral);
        $actualLeadsSales['referral'] = $actualLeadsReferral['lead_needed'];
        
        # digital
        $actualLeadsDigital = $this->setDataActual($dataDigitalTarget);
        $leadDigitalTarget = $this->setDataTarget($dataDigitalTarget, $actualLeadsDigital);
        
        $targetTrackingLead = $this->targetTrackingRepository->getTargetTrackingPeriod(Carbon::now()->startOfMonth()->subMonth(2)->toDateString(), $today, 'lead');
        $targetTrackingRevenue = $this->targetTrackingRepository->getTargetTrackingPeriod(Carbon::now()->startOfMonth()->subMonth(2)->toDateString(), $today, 'revenue');

        # Event
        $events = $this->eventRepository->getEventByMonthyear($today);

        # Chart lead
        $last3month = $currMonth - 2;
            for ($i = 2; $i >= 0; $i--) {
                $dataLeadChart['target'][] = $targetTrackingLead->where('month', $last3month)->count() > 0 ? (int)$targetTrackingLead->where('month', $last3month)->first()->target : 0;
                $dataLeadChart['actual'][] = $targetTrackingLead->where('month', $last3month)->count() > 0 ? (int)$targetTrackingLead->where('month', $last3month)->first()->actual : 0;
                $dataLeadChart['label'][] = Carbon::now()->startOfMonth()->subMonth($i)->format('F');
    
                $dataRevenueChart['target'][] = $targetTrackingRevenue->where('month', $last3month)->count() > 0 ? (int)$targetTrackingRevenue->where('month', $last3month)->first()->target : 0;
                $dataRevenueChart['actual'][] = $targetTrackingRevenue->where('month', $last3month)->count() > 0 ? (int)$targetTrackingRevenue->where('month', $last3month)->first()->actual : 0;
                $dataRevenueChart['label'][] = Carbon::now()->startOfMonth()->subMonth($i)->format('F');
                $last3month++;
            }

        # Day 1-14 (awal bulan)
        $alarmLeads['sales']['always_on']['failed'] = $this->clientLeadTrackingRepository->getFailedLead($today);
        // $alarmLeads['sales']['mid']['lead_needed'] = $actualLeadsSales['lead_needed'] < $leadSalesTarget['lead_needed'] ? true : false;
        $alarmLeads['sales']['mid']['hot_lead'] = $actualLeadsSales['hot_lead'] < $leadSalesTarget['hot_lead'] ? true : false;
        $alarmLeads['sales']['mid']['referral'] = $actualLeadsReferral['lead_needed'] < 10 ? true : false;
        $alarmLeads['digital']['mid']['hot_lead'] = $actualLeadsDigital['hot_lead'] < $leadDigitalTarget['hot_lead'] ? true : false;
        $alarmLeads['general']['mid']['event'] = ($alarmLeads['sales']['mid']['hot_lead'] || $alarmLeads['sales']['mid']['referral']) && $events->count() < 1 ? true : false;
  
            # Day 15-30 (akhir bulan)
            if ($today > date('Y-m') . '-' . $midOfMonth) {
                # sales
                unset($alarmLeads['sales']['mid']['lead_needed']);
                unset($alarmLeads['sales']['mid']['hot_lead']);
                $alarmLeads['sales']['end']['revenue'] = $dataRevenueChart['actual'][2] < ($dataRevenueChart['target'][2] != 0 ? $dataRevenueChart['target'][2] * 50 / 100 : 0) ? true : false;
                $alarmLeads['sales']['end']['ic'] = $actualLeadsSales['ic'] < $leadSalesTarget['ic'] ? true : false;
                $alarmLeads['sales']['end']['hot_lead'] = $actualLeadsSales['hot_lead'] < $leadSalesTarget['hot_lead'] ? true : false;
                
                # digital
                // unset($alarmLeads['digital']['mid']['lead_needed']);
                unset($alarmLeads['digital']['mid']['hot_lead']);
                $alarmLeads['digital']['end']['revenue'] = $dataRevenueChart['actual'][2] < ($dataRevenueChart['target'][2] != 0 ? $dataRevenueChart['target'][2] * 50 / 100 : 0) ? true : false;
                $alarmLeads['digital']['end']['hot_lead'] = $actualLeadsDigital['hot_lead'] < $leadDigitalTarget['hot_lead'] ? true : false;
                $alarmLeads['digital']['end']['lead_needed'] = $actualLeadsDigital['lead_needed'] < $leadDigitalTarget['lead_needed'] ? true : false;
            }

        return $alarmLeads;            
    }
   
    public function countAlarm()
    {
        $alarmLeads = $this->setAlarmLead();
        $count = [
            'sales' => 0,
            'digital' => 0,
            'general' => 0,
        ];
        foreach ($alarmLeads as $divisi => $alarmDivisi) {
            foreach ($alarmDivisi as $alarmTime) {
                foreach ($alarmTime as $key => $alarm) {
                    switch ($divisi) {
                        case 'sales':
                            $alarm == true ? $count['sales']++ : null;
                            break;
                        case 'digital':
                            $alarm == true ? $count['digital']++ : null;
                            break;
                        case 'general':
                            $alarm == true ? $count['sales']++ : null;
                            $alarm == true ? $count['digital']++ : null;
                            break;
                    }
                    
                    $alarm == true ? $count['general']++ : null;
                   
                }
            }
        }

        return $count;
    }

    public function notification()
    {
        $alarmLeads = $this->setAlarmLead();

        $message = null;
        $message['sales'][] = $message['digital'][] = $message['general'][] = null;
        foreach ($alarmLeads as $divisi => $alarmDivisi) {
            foreach ($alarmDivisi as $alarmTime) {
                foreach ($alarmTime as $key => $alarm) {

                    if($alarm){


                        switch ($divisi) {
                            case 'sales':
                                $message['sales'][] = ($key == 'event') ? 'There are no events this month.' : (($key == 'failed') ? 'There are 3 times in a row the Admissions Mentoring program has failed.' : str_replace('_', ' ', $key) . '<b> '.$divisi.'</b> less than target');
                                break;
                            case 'digital':
                                $message['digital'][] = str_replace('_', ' ', $key) . '<b> '.$divisi.'</b> less than target';
                                break;
                            case 'general':
                                $message['sales'][] = $key == 'event' ? 'There are no events this month.' : str_replace('_', ' ', $key) . '<b> '.$divisi.'</b> less than target';
                                $message['digital'][] = $key == 'event' ? 'There are no events this month.' : str_replace('_', ' ', $key) . '<b> '.$divisi.'</b> less than target';
                                break;
                        }
                        $message['general'][] = ($key == 'event') ? 'There are no events this month.' : (($key == 'failed') ? 'There are 3 times in a row the Admissions Mentoring program has failed.' : str_replace('_', ' ', $key) . '<b> '.$divisi.'</b> less than target');
                    }
                }
            }
        }

        return $message;
    }

    private function calculatePercentageLead($actual, $target)
    {
        if ($target == 0)
            return 0;

        return $actual / $target * 100;
    }


}

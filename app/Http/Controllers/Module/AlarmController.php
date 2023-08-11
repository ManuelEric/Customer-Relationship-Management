<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;
use App\Http\Traits\Modules\GetClientStatusTrait;
use App\Interfaces\ClientRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
        $this->leadTargetRepository = $repositories->leadTargetRepository;
    }

    public function get($request)
    {

        $fullDay = Carbon::now()->daysInMonth;
        $midOfMonth = floor($fullDay / 2);
        // $alarm = new Collection();

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

        $actualLeadsSales['referral'] = $actualLeadsReferral['lead_needed'];

        # Gagal 3x berturut-turut
        $alarmLeads['general']['always_on']['failed'] = $this->clientLeadTrackingRepository->getFailedLead($today);

        $targetTrackingLead = $this->targetTrackingRepository->getTargetTrackingPeriod(Carbon::now()->startOfMonth()->subMonth(2)->toDateString(), $today, 'lead');
        $targetTrackingRevenue = $this->targetTrackingRepository->getTargetTrackingPeriod(Carbon::now()->startOfMonth()->subMonth(2)->toDateString(), $today, 'revenue');

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
        $alarmLeads['sales']['mid']['lead_needed'] = $actualLeadsSales['lead_needed'] < $leadSalesTarget['lead_needed'] ? true : false;
        $alarmLeads['sales']['mid']['hot_lead'] = $actualLeadsSales['hot_lead'] < $leadSalesTarget['hot_lead'] ? true : false;
        $alarmLeads['sales']['mid']['referral'] = $actualLeadsReferral['lead_needed'] < 10 ? true : false;
        $alarmLeads['digital']['mid']['hot_lead'] = $actualLeadsDigital['hot_lead'] < (4 * $leadDigitalTarget['hot_lead']) ? true : false;
        $alarmLeads['general']['mid']['event'] = $alarmLeads['sales']['mid']['hot_lead'] || $alarmLeads['sales']['mid']['referral'] && $events->count() < 1 ? true : false;

        # Day 15-30 (akhir bulan)
        if (date('Y-m-d') > date('Y-m') . '-' . $midOfMonth) {
            # sales
            unset($alarmLeads['sales']['mid']['lead_needed']);
            $alarmLeads['sales']['end']['revenue'] = $actualLeadsSales['revenue'] < $dataRevenueChart['target'][2] * 50 / 100 ? true : false;
            $alarmLeads['sales']['end']['IC'] = $actualLeadsSales['IC'] < $leadSalesTarget['IC'] ? true : false;
            $alarmLeads['sales']['end']['hot_lead'] = $actualLeadsSales['hot_lead'] < 2 * $leadSalesTarget['hot_lead'] ? true : false;
            $alarmLeads['sales']['end']['revenue'] = $dataRevenueChart['actual'][2] < $this->calculatePercentageLead($dataRevenueChart['target'][2], 50) ? true : false;

            # digital
            unset($alarmLeads['digital']['mid']['lead_needed']);
            $alarmLeads['digital']['end']['hot_lead'] = $actualLeadsDigital['hot_lead'] < (4 * $leadDigitalTarget['hot_leads']) ? true : false;
            $alarmLeads['digital']['end']['lead_needed'] = $actualLeadsDigital['lead_needed'] < $leadDigitalTarget['lead_needed'] ? true : false;
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
            # alarm
            'alarmLeads' => $alarmLeads,
            'countAlarm' => $this->countAlarm($alarmLeads),
            'notification' => $this->notification($alarmLeads),
            'leadSalesTarget' => $leadSalesTarget,
            'leadReferralTarget' => $leadReferralTarget,
            'leadDigitalTarget' => $leadDigitalTarget,
            'actualLeadsDigital' => $actualLeadsDigital,
            'actualLeadsSales' => $actualLeadsSales,
            'actualLeadsReferral' => $actualLeadsReferral,
            'dataLeads' => $dataLeads,
            'dataLeadChart' => $dataLeadChart,
            'dataRevenueChart' => $dataRevenueChart
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

        return $actual / $target * 100;
    }

    private function setDataActual($dataActual)
    {
        $data = [
            'lead_needed' => isset($dataActual) ? $dataActual->achieved_lead : 0,
            'hot_lead' => isset($dataActual) ? $dataActual->achieved_hotleads : 0,
            'ic' => isset($dataActual) ? $dataActual->achieved_initconsult : 0,
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
        if (isset($dataTarget)) {
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

    private function countAlarm($alarmLeads)
    {
        $count = 0;
        foreach ($alarmLeads as $alarmDivisi) {
            foreach ($alarmDivisi as $alarmTime) {
                foreach ($alarmTime as $key => $alarm) {
                    $alarm == true ? $count++ : null;
                }
            }
        }

        return $count;
    }

    private function notification($alarmLeads)
    {
        foreach ($alarmLeads as $divisi => $alarmDivisi) {
            foreach ($alarmDivisi as $alarmTime) {
                foreach ($alarmTime as $key => $alarm) {
                    if($alarm){
                        $message[] = $key == 'event' ? 'Event' : str_replace('_', ' ', $key) . '<b> '.$divisi.'</b> less than target';
                    }
                }
            }
        }

        return $message;
    }
}

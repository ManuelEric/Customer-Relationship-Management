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

        $this->alarmRepository = $repositories->alarmRepository;
        $this->salesTargetRepository = $repositories->salesTargetRepository;
        $this->clientLeadTrackingRepository = $repositories->clientLeadTrackingRepository;
        $this->targetTrackingRepository = $repositories->targetTrackingRepository;
        $this->targetSignalRepository = $repositories->targetSignalRepository;
        $this->eventRepository = $repositories->eventRepository;
        $this->leadTargetRepository = $repositories->leadTargetRepository;
        $this->leadRepository = $repositories->leadRepository;
    }

    public function get($request)
    {

        $fullDay = Carbon::now()->daysInMonth;
        $midOfMonth = floor($fullDay / 2);
        // $alarm = new Collection();

        $today = date('Y-m-d');
        $currMonth = date('m');
        
        $allTarget = $this->targetTrackingRepository->getAllTargetTrackingMonthly($today);
        $dataSalesTarget = $this->alarmRepository->getDataTarget($today, 'Sales');
        $dataReferralTarget = $this->alarmRepository->getDataTarget($today, 'Referral');
        $dataDigitalTarget = $this->alarmRepository->getDataTarget($today, 'Digital');

        # Event
        $events = $this->eventRepository->getEventByMonthyear($today);

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

        $alarmLeads = $this->alarmRepository->setAlarmLead();

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

}

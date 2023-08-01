<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;
use App\Http\Traits\Modules\GetClientStatusTrait;
use App\Interfaces\ClientRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
    }

    public function get($request)
    {

        # Alarm
        $digitalAlarm = false;
        $triggerEvent = false;
        $fullDay = Carbon::now()->daysInMonth;
        $midOfMonth = floor($fullDay / 2);

        $leedNeeded = 36;
        $referralTarget = 10;

        $today = date('Y-m-d');

        $dataDigitalTarget = $this->targetSignalRepository->getTargetSignalByDivisi('Digital')->first();
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
                'ic' => $dataDigitalTarget->initial_consult_target,
                'hot_lead' => $dataDigitalTarget->hot_leads_target,
                'lead_needed' => $dataDigitalTarget->lead_needed,
                'contribution' => $dataDigitalTarget->contribution_to_target,
            ];
        }

        // $revenueTarget = $dataDigitalTarget->total_target;
        $revenueTarget = 0;

        $clientLead = $this->targetTrackingRepository->getTargetTrackingByMonthYear($today)->where('divisi', 'Sales');

        # Total Leads
        $totalLeads = $clientLead->count() > 0 ? $clientLead->achieved : 0;
        
        // Admission hot lead dari ic
        $totalHotLead = $clientLead->count() > 0 ? $clientLead->achieved : 0;

        # Gagal 3x berturut-turut
        $failLeads = $this->clientLeadTrackingRepository->getInitialConsult($today, 'fail');
        $countFail = 0;
        if(isset($failLeads) > 0){
            foreach ($failLeads as $failLead) {
                $failLead->status == 2 ? $countFail++ : $countFail--;
            }
        }
        $isFailed = $countFail == 3 ? true : false;

        # LS005 is Referral
        $totalReferralLead = 0;

        # Day 1-14 (awal bulan)
        // $digitalAlarm['mid']['lead_needed'] = $totalLeads < $leadDigitalTarget['lead_needed'] ? true : false;
        $digitalAlarm['mid']['hot_lead'] = $totalHotLead < (4*$leadDigitalTarget['hot_lead']) ? true : false;
        $referralAlarm = $totalReferralLead < 10 ? true : false;

        $triggerEvent = $digitalAlarm['mid']['hot_lead'] || $referralAlarm ? true : false;

        // $revenue = $this->clientLeadTrackingRepository->getRevenue($today);
        $revenue = null;
        $totalRevenue = $revenue != null ? $revenue->sum('total') : 0;
        // $totalRevenue = 125000000;

        # Day 15-30 (akhir bulan)
        if (date('Y-m-d') > date('Y-m') . '-' . $midOfMonth) {
            unset($digitalAlarm['mid']['lead_needed']);
            $digitalAlarm['end']['hot_lead'] = $totalHotLead < (4*$leadDigitalTarget['hot_leads']) ? true : false;
            $digitalAlarm['end']['lead_needed'] = $totalLeads < 2*$leedNeeded ? true : false;
        }

        # Always On 
        $digitalAlarm['always_on'] = $isFailed;

        $actualLeadsDigital = [
            'lead_needed' => $totalLeads,
            'hot_lead' => $totalHotLead,
            'IC' => $totalHotLead,
            'referral' => $totalReferralLead,
            'revenue' => $totalRevenue,
            'contribution' => 0,
        ];

        $leadDigitalTarget['percentage_lead_needed'] = $actualLeadsDigital['lead_needed']/$leadDigitalTarget['lead_needed']*100;
        $leadDigitalTarget['percentage_hot_lead'] = $actualLeadsDigital['hot_lead']/$leadDigitalTarget['hot_lead']*100;
        $leadDigitalTarget['percentage_ic'] = $actualLeadsDigital['IC']/$leadDigitalTarget['ic']*100;
        $leadDigitalTarget['percentage_contribution'] = $actualLeadsDigital['contribution']/$leadDigitalTarget['contribution']*100;

        # === end Alarm ===


        # INITIALIZE PARAMETERS START
        $month = date('Y-m');
      
        $response_ofDigitalStatus = [

            # alarm
            'digitalAlarm' => $digitalAlarm,
            'triggerEvent' => $triggerEvent,
            'actualLeadsDigital' => $actualLeadsDigital,
            'leadDigitalTarget' => $leadDigitalTarget,
        ];

        return $response_ofDigitalStatus;
    }


}

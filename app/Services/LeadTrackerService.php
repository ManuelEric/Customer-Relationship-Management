<?php

namespace App\Services;

use App\Interfaces\ClientLogRepositoryInterface;
use Illuminate\Support\Carbon;

class LeadTrackerService 
{
    private ClientLogRepositoryInterface $clientLogRepository;
    
    public function __construct(ClientLogRepositoryInterface $clientLogRepository)
    {
        $this->clientLogRepository = $clientLogRepository;
    }

    public function summary(Carbon $start_date, Carbon $end_date)
    {
        return [
            'unfiltered_leads' => [
                'online_paid' => $online_paid = $this->clientLogRepository->unfilteredOnlinePaidLeads($start_date, $end_date)[0],
                'online_organic' => $online_organic = $this->clientLogRepository->unfilteredOnlineOrganicLeads($start_date, $end_date)[0],
                'offline' => $offline = $this->clientLogRepository->unfilteredOfflineLeads($start_date, $end_date)[0],
                'referral' => $referral = $this->clientLogRepository->unfilteredReferralLeads($start_date, $end_date)[0],
                'total' => $total_unfiltered_leads = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => 'N/A',
            ],
            'filtered_leads' => [
                'online_paid' => $online_paid = $this->clientLogRepository->filteredOnlinePaidLeads($start_date, $end_date)[0],
                'online_organic' => $online_organic = $this->clientLogRepository->filteredOnlineOrganicLeads($start_date, $end_date)[0],
                'offline' => $offline = $this->clientLogRepository->filteredOfflineLeads($start_date, $end_date)[0],
                'referral' => $referral = $this->clientLogRepository->filteredReferralSales($start_date, $end_date)[0],
                'total' => $total_filtered_leads = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => toPercentage($total_unfiltered_leads, $total_filtered_leads) . '%',
            ],
            'potential_leads' => [
                'online_paid' => $online_paid = $this->clientLogRepository->potentialOnlinePaidLeads($start_date, $end_date)[0],
                'online_organic' => $online_organic = $this->clientLogRepository->potentialOnlineOrganicLeads($start_date, $end_date)[0],
                'offline' => $offline = $this->clientLogRepository->potentialOfflineLeads($start_date, $end_date)[0],
                'referral' => $referral = $this->clientLogRepository->potentialReferralExistingClientLeads($start_date, $end_date)[0],
                'total' => $total_potential_leads = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => toPercentage($total_filtered_leads, $total_potential_leads) . '%',
            ],
            'deal' => [
                'online_paid' => $online_paid = $this->clientLogRepository->dealOnlinePaidLeads($start_date, $end_date),
                'online_organic' => $online_organic = $this->clientLogRepository->dealOnlineOrganicLeads($start_date, $end_date),
                'offline' => $offline = $this->clientLogRepository->dealOfflineLeads($start_date, $end_date),
                'referral' => $referral = $this->clientLogRepository->dealReferralExistingClientLeads($start_date, $end_date),
                'total' => $total_deal = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => toPercentage($total_potential_leads, $total_deal) . '%',
            ],
            'payment' => [
                'online_paid' => $online_paid = $this->clientLogRepository->paymentOnlinePaidLeads($start_date, $end_date),
                'online_organic' => $online_organic = $this->clientLogRepository->paymentOnlineOrganicLeads($start_date, $end_date),
                'offline' => $offline = $this->clientLogRepository->paymentOfflineLeads($start_date, $end_date),
                'referral' => $referral = $this->clientLogRepository->paymentReferralExistingClientLeads($start_date, $end_date),
                'total' => $total_payment = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => toPercentage($total_deal, $total_payment) . '%',
            ]
        ];
    }

    public function leadMentoring(Carbon $start_date, Carbon $end_date)
    {
        return [
            'potential_leads' => [
                'online_paid' => $online_paid = $this->clientLogRepository->mentoringOnlinePaidPotentialLeads($start_date, $end_date)[0],
                'online_organic' => $online_organic = $this->clientLogRepository->mentoringOnlineOrganicPotentialLeads($start_date, $end_date)[0],
                'offline' => $offline = $this->clientLogRepository->mentoringOfflinePotentialLeads($start_date, $end_date)[0],
                'referral' => $referral = $this->clientLogRepository->mentoringReferralPotentialLeads($start_date, $end_date)[0],
                'total' => $total_potential = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => 'N/A',
            ],
            'assessment_form' => [
                'online_paid' => $online_paid = $this->clientLogRepository->mentoringOnlinePaidAssessmentForm($start_date, $end_date)[0],
                'online_organic' => $online_organic = $this->clientLogRepository->mentoringOnlineOrganicAssessmentForm($start_date, $end_date)[0],
                'offline' => $offline = $this->clientLogRepository->mentoringOfflineAssessmentForm($start_date, $end_date)[0],
                'referral' => $referral = $this->clientLogRepository->mentoringReferralAssessmentForm($start_date, $end_date)[0],
                'total' => $total_assessment = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => toPercentage($total_potential, $total_assessment) .'%',
            ],
            'IC' => [
                'online_paid' => $online_paid = $this->clientLogRepository->mentoringOnlinePaidIC($start_date, $end_date)[0],
                'online_organic' => $online_organic = $this->clientLogRepository->mentoringOnlineOrganicIC($start_date, $end_date)[0],
                'offline' => $offline = $this->clientLogRepository->mentoringOfflineIC($start_date, $end_date)[0],
                'referral' => $referral = $this->clientLogRepository->mentoringReferralIC($start_date, $end_date)[0],
                'total' => $total_ic = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => toPercentage($total_potential, $total_ic) .'%',
            ],
            'IA' => [
                'online_paid' => $online_paid = $this->clientLogRepository->mentoringOnlinePaidIA($start_date, $end_date),
                'online_organic' => $online_organic = $this->clientLogRepository->mentoringOnlineOrganicIA($start_date, $end_date),
                'offline' => $offline = $this->clientLogRepository->mentoringOfflineIA($start_date, $end_date),
                'referral' => $referral = $this->clientLogRepository->mentoringReferralIA($start_date, $end_date),
                'total' => $total_ia = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => toPercentage($total_potential, $total_ia) .'%',
            ],
            'deal' => [
                'online_paid' => $online_paid = $this->clientLogRepository->mentoringOnlinePaidDeal($start_date, $end_date),
                'online_organic' => $online_organic = $this->clientLogRepository->mentoringOnlineOrganicDeal($start_date, $end_date),
                'offline' => $offline = $this->clientLogRepository->mentoringOfflineDeal($start_date, $end_date),
                'referral' => $referral = $this->clientLogRepository->mentoringReferralDeal($start_date, $end_date),
                'total' => $total_deal = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => toPercentage($total_ia, $total_deal) .'%',
            ],
            'agreement' => [
                'online_paid' => $online_paid = $this->clientLogRepository->mentoringOnlinePaidAgreement($start_date, $end_date),
                'online_organic' => $online_organic = $this->clientLogRepository->mentoringOnlineOrganicAgreement($start_date, $end_date),
                'offline' => $offline = $this->clientLogRepository->mentoringOfflineAgreement($start_date, $end_date),
                'referral' => $referral = $this->clientLogRepository->mentoringReferralAgreement($start_date, $end_date),
                'total' => $total_agreement = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => toPercentage($total_deal, $total_agreement) .'%',
            ],
            'payment' => [
                'online_paid' => $online_paid = $this->clientLogRepository->mentoringOnlinePaidPayment($start_date, $end_date),
                'online_organic' => $online_organic = $this->clientLogRepository->mentoringOnlineOrganicPayment($start_date, $end_date),
                'offline' => $offline = $this->clientLogRepository->mentoringOfflinePayment($start_date, $end_date),
                'referral' => $referral = $this->clientLogRepository->mentoringReferralPayment($start_date, $end_date),
                'total' => $total_payment = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => toPercentage($total_agreement, $total_payment) .'%',
            ]
        ];
    }

    public function leadMentoringOnSales(Carbon $start_date, Carbon $end_date)
    {
        [$potentials, $total_to_date] = $this->clientLogRepository->mentoringPotentialLeadsTotalToDate($start_date, $end_date);
        [$assessment_form, $total_assessment] = $this->clientLogRepository->mentoringAssessmentFormTotalToDate($potentials, $start_date, $end_date);
        [$initial_consult, $total_initial_consult] = $this->clientLogRepository->mentoringICTotalToDate( $potentials, $start_date, $end_date);
        $total_initial_assessment = $this->clientLogRepository->mentoringIATotalToDate($potentials, $start_date, $end_date);

        return [
            'potential_leads' => $total_to_date,
            'assessment_form' => $total_assessment,
            'IC' => $total_initial_consult,
            'IA' => $total_initial_assessment,
            'deal' => $this->clientLogRepository->mentoringDealTotalToDate($start_date, $end_date),
            'agreement' => $this->clientLogRepository->mentoringAgreementTotalToDate($start_date, $end_date),
            'payment' => $this->clientLogRepository->mentoringPaymentTotalToDate($start_date, $end_date)
        ];
    }

    public function leadTutoring(Carbon $start_date, Carbon $end_date)
    {
        return [
            'potential_leads' => [
                'online_paid' => $online_paid = $this->clientLogRepository->tutoringOnlinePaidPotentialLeads($start_date, $end_date)[0],
                'online_organic' => $online_organic = $this->clientLogRepository->tutoringOnlineOrganicPotentialLeads($start_date, $end_date)[0],
                'offline' => $offline = $this->clientLogRepository->tutoringOfflinePotentialLeads($start_date, $end_date)[0],
                'referral' => $referral = $this->clientLogRepository->tutoringReferralPotentialLeads($start_date, $end_date)[0],
                'total' => $total_potential = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => 'N/A',
            ],
            'trial_date' => [
                'online_paid' => $online_paid = $this->clientLogRepository->tutoringOnlinePaidTrialDate($start_date, $end_date),
                'online_organic' => $online_organic = $this->clientLogRepository->tutoringOnlineOrganicTrialDate($start_date, $end_date),
                'offline' => $offline = $this->clientLogRepository->tutoringOfflineTrialDate($start_date, $end_date),
                'referral' => $referral = $this->clientLogRepository->tutoringReferralTrialDate($start_date, $end_date),
                'total' => $total_trial = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => toPercentage($total_potential, $total_trial) . '%',
            ],
            'deal' => [
                'online_paid' => $online_paid = $this->clientLogRepository->tutoringOnlinePaidDeal($start_date, $end_date),
                'online_organic' => $online_organic = $this->clientLogRepository->tutoringOnlineOrganicDeal($start_date, $end_date),
                'offline' => $offline = $this->clientLogRepository->tutoringOfflineDeal($start_date, $end_date),
                'referral' => $referral = $this->clientLogRepository->tutoringReferralDeal($start_date, $end_date),
                'total' => $total_deal = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => toPercentage($total_trial, $total_deal) . '%',
            ],
            'payment' => [
                'online_paid' => $online_paid = $this->clientLogRepository->tutoringOnlinePaidPayment($start_date, $end_date),
                'online_organic' => $online_organic = $this->clientLogRepository->tutoringOnlineOrganicPayment($start_date, $end_date),
                'offline' => $offline = $this->clientLogRepository->tutoringOfflinePayment($start_date, $end_date),
                'referral' => $referral = $this->clientLogRepository->tutoringReferralPayment($start_date, $end_date),
                'total' => $total_payment = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => toPercentage($total_deal, $total_payment) . '%',
            ]
        ];
    }

    public function leadTutoringOnSales(Carbon $start_date, Carbon $end_date)
    {
        [$potentials, $total_to_date] = $this->clientLogRepository->tutoringPotentialLeadsTotalToDate($start_date, $end_date);
        $total_trial_date = $this->clientLogRepository->tutoringTrialDateTotalToDate($potentials, $start_date, $end_date);

        return [
            'potential_leads' => $total_to_date,
            'trial_date' => $total_trial_date,
            'deal' => $this->clientLogRepository->tutoringDealTotalToDate($start_date, $end_date),
            'payment' => $this->clientLogRepository->tutoringPaymentTotalToDate($start_date, $end_date)
        ];
    }

    public function leadGIP(Carbon $start_date, Carbon $end_date)
    {
        return [
            'potential_leads' => [
                'online_paid' => $online_paid = $this->clientLogRepository->GIPOnlinePaidPotentialLeads($start_date, $end_date),
                'online_organic' => $online_organic = $this->clientLogRepository->GIPOnlineOrganicPotentialLeads($start_date, $end_date),
                'offline' => $offline = $this->clientLogRepository->GIPOfflinePotentialLeads($start_date, $end_date),
                'referral' => $referral = $this->clientLogRepository->GIPReferralPotentialLeads($start_date, $end_date),
                'total' => $total_potential = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => 'N/A',
            ],
            'deal' => [
                'online_paid' => $online_paid = $this->clientLogRepository->GIPOnlinePaidDeal($start_date, $end_date),
                'online_organic' => $online_organic = $this->clientLogRepository->GIPOnlineOrganicDeal($start_date, $end_date),
                'offline' => $offline = $this->clientLogRepository->GIPOfflineDeal($start_date, $end_date),
                'referral' => $referral = $this->clientLogRepository->GIPReferralDeal($start_date, $end_date),
                'total' => $total_deal = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => toPercentage($total_potential, $total_deal) .'%',
            ],
            'payment' => [
                'online_paid' => $online_paid = $this->clientLogRepository->GIPOnlinePaidPayment($start_date, $end_date),
                'online_organic' => $online_organic = $this->clientLogRepository->GIPOnlineOrganicPayment($start_date, $end_date),
                'offline' => $offline = $this->clientLogRepository->GIPOfflinePayment($start_date, $end_date),
                'referral' => $referral = $this->clientLogRepository->GIPReferralPayment($start_date, $end_date),
                'total' => $total_payment = $online_paid + $online_organic + $offline + $referral,
                'conversion_rate' => toPercentage($total_deal, $total_payment) .'%',
            ]
        ];
    }

    public function leadGIPOnSales(Carbon $start_date, Carbon $end_date)
    {
        return [
            'potential_leads' => $this->clientLogRepository->GIPPotentialLeadsTotalToDate($start_date, $end_date),
            'deal' => $this->clientLogRepository->GIPDealTotalToDate($start_date, $end_date),
            'payment' => $this->clientLogRepository->GIPPaymentTotalToDate($start_date, $end_date)
        ];
    }

    public function detailLead(String $type, $date_range)
    {  
        [$start_date, $end_date] = ($date_range) ? array_map([$this, "castToCarbon"], explode('-', $date_range)) : $this->selectCurrentWeek();
        $end_date = $end_date->endOfDay();
        return $this->clientLogRepository->getDetailLeadTracking($type, $start_date, $end_date);
    }

    private function castToCarbon(String $item): Carbon
    {
        return Carbon::parse($item);
    }

    private function selectCurrentWeek(): Array
    {
        $week_start_date = Carbon::now()->startOfWeek();
        $week_end_date = Carbon::now()->endOfWeek();
        return [$week_start_date, $week_end_date];
    }
}
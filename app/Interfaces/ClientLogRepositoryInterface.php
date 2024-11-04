<?php

namespace App\Interfaces;

use Illuminate\Support\Carbon;

interface ClientLogRepositoryInterface
{
    public function getClientLogByClientUUID($clientUUID);
    public function updateClientLogByClientUUID($clientUUID, $new_client_log_details);
    public function deleteClientLogByClientProgIdAndClientUUID($clientprog_id, $client_uuid);

    /**
     *  Unfiltered / Raw 
     * @param \Illuminate\Support\Carbon $start_date
     * @param \Illuminate\Support\Carbon $end_date
     * @return int
     */
    public function unfilteredOnlinePaidLeads(Carbon $start_date, Carbon $end_date): Array;
    public function unfilteredOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): Array;
    public function unfilteredOfflineLeads(Carbon $start_date, Carbon $end_date): Array;
    public function unfilteredReferralLeads(Carbon $start_date, Carbon $end_date): Array;

    /**
     * Filtered / New
     * @param \Illuminate\Support\Carbon $start_date
     * @param \Illuminate\Support\Carbon $end_date
     * @return int
     */
    public function filteredOnlinePaidLeads(Carbon $start_date, Carbon $end_date): Array;
    public function filteredOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): Array;
    public function filteredOfflineLeads(Carbon $start_date, Carbon $end_date): Array;
    public function filteredReferralSales(Carbon $start_date, Carbon $end_date): Array;

    /**
     * Potential
     * @param \Illuminate\Support\Carbon $start_date
     * @param \Illuminate\Support\Carbon $end_date
     * @return int
     */
    public function potentialOnlinePaidLeads(Carbon $start_date, Carbon $end_date): Array;
    public function potentialOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): Array;
    public function potentialOfflineLeads(Carbon $start_date, Carbon $end_date): Array;
    public function potentialReferralExistingClientLeads(Carbon $start_date, Carbon $end_date): Array;

    /**
     * Deal
     * @param \Illuminate\Support\Carbon $start_date
     * @param \Illuminate\Support\Carbon $end_date
     * @return int
     */
    public function dealOnlinePaidLeads(Carbon $start_date, Carbon $end_date): Int;
    public function dealOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): Int;
    public function dealOfflineLeads(Carbon $start_date, Carbon $end_date): Int;
    public function dealReferralExistingClientLeads(Carbon $start_date, Carbon $end_date): Int;

    /**
     * Agreement
     * @param \Illuminate\Support\Carbon $start_date
     * @param \Illuminate\Support\Carbon $end_date
     * @return int
     */
    public function agreementOnlinePaidLeads(Carbon $start_date, Carbon $end_date): Int;
    public function agreementOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): Int;
    public function agreementOfflineLeads(Carbon $start_date, Carbon $end_date): Int;
    public function agreementReferralExistingClientLeads(Carbon $start_date, Carbon $end_date): Int;

    /**
     * Payment
     * @param \Illuminate\Support\Carbon $start_date
     * @param \Illuminate\Support\Carbon $end_date
     * @return int
     */
    public function paymentOnlinePaidLeads(Carbon $start_date, Carbon $end_date): Int;
    public function paymentOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): Int;
    public function paymentOfflineLeads(Carbon $start_date, Carbon $end_date): Int;
    public function paymentReferralExistingClientLeads(Carbon $start_date, Carbon $end_date): Int;

    /**
     *  Data by Product category 
     *  Mentoring
     * 
     *
     * Potential Leads
     */
    public function mentoringOnlinePaidPotentialLeads(Carbon $start_date, Carbon $end_date): Array;
    public function mentoringOnlineOrganicPotentialLeads(Carbon $start_date, Carbon $end_date): Array;
    public function mentoringOfflinePotentialLeads(Carbon $start_date, Carbon $end_date): Array;
    public function mentoringReferralPotentialLeads(Carbon $start_date, Carbon $end_date): Array;

    /**
     * Assessment Form
     */
    public function mentoringOnlinePaidAssessmentForm(Carbon $start_date, Carbon $end_date): Array;
    public function mentoringOnlineOrganicAssessmentForm(Carbon $start_date, Carbon $end_date): Array;
    public function mentoringOfflineAssessmentForm(Carbon $start_date, Carbon $end_date): Array;
    public function mentoringReferralAssessmentForm(Carbon $start_date, Carbon $end_date): Array;

    /**
     * Initial Consult (IC)
     */
    public function mentoringOnlinePaidIC(Carbon $start_date, Carbon $end_date): Array;
    public function mentoringOnlineOrganicIC(Carbon $start_date, Carbon $end_date): Array;
    public function mentoringOfflineIC(Carbon $start_date, Carbon $end_date): Array;
    public function mentoringReferralIC(Carbon $start_date, Carbon $end_date): Array;

    /**
     * Initial Assessment (IAR/IA)
     */
    public function mentoringOnlinePaidIA(Carbon $start_date, Carbon $end_date): Int;
    public function mentoringOnlineOrganicIA(Carbon $start_date, Carbon $end_date): Int;
    public function mentoringOfflineIA(Carbon $start_date, Carbon $end_date): Int;
    public function mentoringReferralIA(Carbon $start_date, Carbon $end_date): Int;

    /**
     * Deal
     */
    public function mentoringOnlinePaidDeal(Carbon $start_date, Carbon $end_date): Int;    
    public function mentoringOnlineOrganicDeal(Carbon $start_date, Carbon $end_date): Int;    
    public function mentoringOfflineDeal(Carbon $start_date, Carbon $end_date): Int;    
    public function mentoringReferralDeal(Carbon $start_date, Carbon $end_date): Int;    

    /**
     * Agreement
     * @return void
     */
    public function mentoringOnlinePaidAgreement(Carbon $start_date, Carbon $end_date): Int;
    public function mentoringOnlineOrganicAgreement(Carbon $start_date, Carbon $end_date): Int;
    public function mentoringOfflineAgreement(Carbon $start_date, Carbon $end_date): Int;
    public function mentoringReferralAgreement(Carbon $start_date, Carbon $end_date): Int;

    /**
     * Payment
     */
    public function mentoringOnlinePaidPayment(Carbon $start_date, Carbon $end_date): Int;
    public function mentoringOnlineOrganicPayment(Carbon $start_date, Carbon $end_date): Int;
    public function mentoringOfflinePayment(Carbon $start_date, Carbon $end_date): Int;
    public function mentoringReferralPayment(Carbon $start_date, Carbon $end_date): Int;


    /**
     * 
     *  Data by Sales
     *  Mentoring
     * 
     * 
     */
    public function mentoringPotentialLeadsTotalToDate(Carbon $start_date, Carbon $end_date);
    /**
     * Assessment Form
     */
    public function mentoringAssessmentFormTotalToDate($potentials, Carbon $start_date, Carbon $end_date);

    /**
     * Initial Consult
     */
    public function mentoringICTotalToDate($potentials, Carbon $start_date, Carbon $end_date);

    /**
     * Initial Assessment Request IAR / IA
     */
    public function mentoringIATotalToDate($potentials, Carbon $start_date, Carbon $end_date);

    /**
     * Deal
     */
    public function mentoringDealTotalToDate(Carbon $start_date, Carbon $end_date);

    /**
     * Agreement
     */
    public function mentoringAgreementTotalToDate(Carbon $start_date, Carbon $end_date);

    /**
     * Payment
     */
    public function mentoringPaymentTotalToDate(Carbon $start_date, Carbon $end_date);


    /**
     * leads of tutoring
     * @return void
     */
    public function tutoringOnlinePaidPotentialLeads(Carbon $start_date, Carbon $end_date);
    public function tutoringOnlineOrganicPotentialLeads(Carbon $start_date, Carbon $end_date);
    public function tutoringOfflinePotentialLeads(Carbon $start_date, Carbon $end_date);
    public function tutoringReferralPotentialLeads(Carbon $start_date, Carbon $end_date);
    public function tutoringOnlinePaidTrialDate(Carbon $start_date, Carbon $end_date);
    public function tutoringOnlineOrganicTrialDate(Carbon $start_date, Carbon $end_date);
    public function tutoringOfflineTrialDate(Carbon $start_date, Carbon $end_date);
    public function tutoringReferralTrialDate(Carbon $start_date, Carbon $end_date);
    public function tutoringOnlinePaidDeal(Carbon $start_date, Carbon $end_date);
    public function tutoringOnlineOrganicDeal(Carbon $start_date, Carbon $end_date);
    public function tutoringOfflineDeal(Carbon $start_date, Carbon $end_date);
    public function tutoringReferralDeal(Carbon $start_date, Carbon $end_date);
    public function tutoringOnlinePaidPayment(Carbon $start_date, Carbon $end_date);
    public function tutoringOnlineOrganicPayment(Carbon $start_date, Carbon $end_date);
    public function tutoringOfflinePayment(Carbon $start_date, Carbon $end_date);
    public function tutoringReferralPayment(Carbon $start_date, Carbon $end_date);   

    /**
     * 
     *  Data by Sales
     *  Tutoring
     * 
     * 
     */
    public function tutoringPotentialLeadsTotalToDate(Carbon $start_date, Carbon $end_date);
    public function tutoringTrialDateTotalToDate($potentials, Carbon $start_date, Carbon $end_date);
    public function tutoringDealTotalToDate(Carbon $start_date, Carbon $end_date);
    public function tutoringPaymentTotalToDate(Carbon $start_date, Carbon $end_date);
    
    /**
     * Leads of GIP
     * @param \Illuminate\Support\Carbon $start_date
     * @param \Illuminate\Support\Carbon $end_date
     * @return void
     */
    public function gipOnlinePaidPotentialLeads(Carbon $start_date, Carbon $end_date): Int;
    public function gipOnlineOrganicPotentialLeads(Carbon $start_date, Carbon $end_date);
    public function gipOfflinePotentialLeads(Carbon $start_date, Carbon $end_date);
    public function gipReferralPotentialLeads(Carbon $start_date, Carbon $end_date);
    public function gipOnlinePaidDeal(Carbon $start_date, Carbon $end_date);
    public function gipOnlineOrganicDeal(Carbon $start_date, Carbon $end_date);
    public function gipOfflineDeal(Carbon $start_date, Carbon $end_date);
    public function gipReferralDeal(Carbon $start_date, Carbon $end_date);
    public function gipOnlinePaidPayment(Carbon $start_date, Carbon $end_date);
    public function gipOnlineOrganicPayment(Carbon $start_date, Carbon $end_date);
    public function gipOfflinePayment(Carbon $start_date, Carbon $end_date);
    public function gipReferralPayment(Carbon $start_date, Carbon $end_date);

    /**
     * 
     *  Data by Sales
     *  Tutoring
     * 
     * 
     */
    public function gipPotentialLeadsTotalToDate(Carbon $start_date, Carbon $end_date);
    public function gipDealTotalToDate(Carbon $start_date, Carbon $end_date);
    public function gipPaymentTotalToDate(Carbon $start_date, Carbon $end_date);
}
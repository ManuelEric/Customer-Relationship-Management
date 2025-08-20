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
     *
     * @return int
     */
    public function unfilteredOnlinePaidLeads(Carbon $start_date, Carbon $end_date): array;

    public function unfilteredOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): array;

    public function unfilteredOfflineLeads(Carbon $start_date, Carbon $end_date): array;

    public function unfilteredReferralLeads(Carbon $start_date, Carbon $end_date): array;

    /**
     * Filtered / New
     *
     * @return int
     */
    public function filteredOnlinePaidLeads(Carbon $start_date, Carbon $end_date): array;

    public function filteredOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): array;

    public function filteredOfflineLeads(Carbon $start_date, Carbon $end_date): array;

    public function filteredReferralSales(Carbon $start_date, Carbon $end_date): array;

    /**
     * Potential
     *
     * @return int
     */
    public function potentialOnlinePaidLeads(Carbon $start_date, Carbon $end_date): array;

    public function potentialOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): array;

    public function potentialOfflineLeads(Carbon $start_date, Carbon $end_date): array;

    public function potentialReferralExistingClientLeads(Carbon $start_date, Carbon $end_date): array;

    /**
     * Deal
     */
    public function dealOnlinePaidLeads(Carbon $start_date, Carbon $end_date): int;

    public function dealOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): int;

    public function dealOfflineLeads(Carbon $start_date, Carbon $end_date): int;

    public function dealReferralExistingClientLeads(Carbon $start_date, Carbon $end_date): int;

    /**
     * Agreement
     */
    public function agreementOnlinePaidLeads(Carbon $start_date, Carbon $end_date): int;

    public function agreementOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): int;

    public function agreementOfflineLeads(Carbon $start_date, Carbon $end_date): int;

    public function agreementReferralExistingClientLeads(Carbon $start_date, Carbon $end_date): int;

    /**
     * Payment
     */
    public function paymentOnlinePaidLeads(Carbon $start_date, Carbon $end_date): int;

    public function paymentOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): int;

    public function paymentOfflineLeads(Carbon $start_date, Carbon $end_date): int;

    public function paymentReferralExistingClientLeads(Carbon $start_date, Carbon $end_date): int;

    /**
     *  Data by Product category
     *  Mentoring
     *
     *
     * Potential Leads
     */
    public function mentoringOnlinePaidPotentialLeads(Carbon $start_date, Carbon $end_date): array;

    public function mentoringOnlineOrganicPotentialLeads(Carbon $start_date, Carbon $end_date): array;

    public function mentoringOfflinePotentialLeads(Carbon $start_date, Carbon $end_date): array;

    public function mentoringReferralPotentialLeads(Carbon $start_date, Carbon $end_date): array;

    /**
     * Assessment Form
     */
    public function mentoringOnlinePaidAssessmentForm(Carbon $start_date, Carbon $end_date): array;

    public function mentoringOnlineOrganicAssessmentForm(Carbon $start_date, Carbon $end_date): array;

    public function mentoringOfflineAssessmentForm(Carbon $start_date, Carbon $end_date): array;

    public function mentoringReferralAssessmentForm(Carbon $start_date, Carbon $end_date): array;

    /**
     * Initial Consult (IC)
     */
    public function mentoringOnlinePaidIC(Carbon $start_date, Carbon $end_date): array;

    public function mentoringOnlineOrganicIC(Carbon $start_date, Carbon $end_date): array;

    public function mentoringOfflineIC(Carbon $start_date, Carbon $end_date): array;

    public function mentoringReferralIC(Carbon $start_date, Carbon $end_date): array;

    /**
     * Initial Assessment (IAR/IA)
     */
    public function mentoringOnlinePaidIA(Carbon $start_date, Carbon $end_date): int;

    public function mentoringOnlineOrganicIA(Carbon $start_date, Carbon $end_date): int;

    public function mentoringOfflineIA(Carbon $start_date, Carbon $end_date): int;

    public function mentoringReferralIA(Carbon $start_date, Carbon $end_date): int;

    /**
     * Deal
     */
    public function mentoringOnlinePaidDeal(Carbon $start_date, Carbon $end_date): int;

    public function mentoringOnlineOrganicDeal(Carbon $start_date, Carbon $end_date): int;

    public function mentoringOfflineDeal(Carbon $start_date, Carbon $end_date): int;

    public function mentoringReferralDeal(Carbon $start_date, Carbon $end_date): int;

    /**
     * Agreement
     *
     * @return void
     */
    public function mentoringOnlinePaidAgreement(Carbon $start_date, Carbon $end_date): int;

    public function mentoringOnlineOrganicAgreement(Carbon $start_date, Carbon $end_date): int;

    public function mentoringOfflineAgreement(Carbon $start_date, Carbon $end_date): int;

    public function mentoringReferralAgreement(Carbon $start_date, Carbon $end_date): int;

    /**
     * Payment
     */
    public function mentoringOnlinePaidPayment(Carbon $start_date, Carbon $end_date): int;

    public function mentoringOnlineOrganicPayment(Carbon $start_date, Carbon $end_date): int;

    public function mentoringOfflinePayment(Carbon $start_date, Carbon $end_date): int;

    public function mentoringReferralPayment(Carbon $start_date, Carbon $end_date): int;

    /**
     *  Data by Sales
     *  Mentoring
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
     *
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
     *  Data by Sales
     *  Tutoring
     */
    public function tutoringPotentialLeadsTotalToDate(Carbon $start_date, Carbon $end_date);

    public function tutoringTrialDateTotalToDate($potentials, Carbon $start_date, Carbon $end_date);

    public function tutoringDealTotalToDate(Carbon $start_date, Carbon $end_date);

    public function tutoringPaymentTotalToDate(Carbon $start_date, Carbon $end_date);

    /**
     * Leads of GIP
     *
     * @return void
     */
    public function gipOnlinePaidPotentialLeads(Carbon $start_date, Carbon $end_date): int;

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
     *  Data by Sales
     *  Tutoring
     */
    public function gipPotentialLeadsTotalToDate(Carbon $start_date, Carbon $end_date);

    public function gipDealTotalToDate(Carbon $start_date, Carbon $end_date);

    public function gipPaymentTotalToDate(Carbon $start_date, Carbon $end_date);

    /**
     * Detail Lead
     */
    public function getDetailLeadTracking(string $type, Carbon $start_date, Carbon $end_date, ?array $search = []);
}

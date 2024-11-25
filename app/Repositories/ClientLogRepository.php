<?php

namespace App\Repositories;

use App\Interfaces\ClientLogRepositoryInterface;
use App\Models\ClientLog;
use App\Models\ClientProgram;
use Illuminate\Support\Carbon;

class ClientLogRepository implements ClientLogRepositoryInterface 
{
    public function getClientLogByClientUUID($clientUUID)
    {
        return ClientLog::where('client_id', $clientUUID)->get();
    }

    public function updateClientLogByClientUUID($clientUUID, $new_client_log_details)
    {
        return ClientLog::where('client_id', $clientUUID)->update($new_client_log_details);
    }

    public function deleteClientLogByClientProgIdAndClientUUID($clientprog_id, $client_id)
    {
        return ClientLog::where('clientprog_id', $clientprog_id)->where('client_id', $client_id)->delete();
    }


    /**
     * Summary of unfilteredOnlinePaidLeads
     * @param \Illuminate\Support\Carbon $start_date
     * @param \Illuminate\Support\Carbon $end_date
     * @return int
     */
    public function unfilteredOnlinePaidLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $query = ClientLog::onlinePaidUnfilteredLeads()->whereBetween('created_at', [$start_date, $end_date]);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray(),
        ];
    }
    
    public function unfilteredOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $query = ClientLog::onlineOrganicUnfilteredLeads()->whereBetween('created_at', [$start_date, $end_date]);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function unfilteredOfflineLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $query = ClientLog::offlineUnfilteredLeads()->whereBetween('created_at', [$start_date, $end_date]);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function unfilteredReferralLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $query = ClientLog::referralFromExistingClientsUnfilteredLeads()->whereBetween('created_at', [$start_date, $end_date]);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }
    
    /**
     * Summary of filteredOnlinePaidLeads
     * @param \Illuminate\Support\Carbon $start_date
     * @param \Illuminate\Support\Carbon $end_date
     * @return int
     */
    public function filteredOnlinePaidLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $unfiltered_leads = $this->unfilteredOnlinePaidLeads($start_date, $end_date)[1];
        $query = ClientLog::onlinePaidFilteredLeads()->whereBetween('created_at', [$start_date, $end_date])->whereIn('client_id', $unfiltered_leads);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function filteredOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $unfiltered_leads = $this->unfilteredOnlineOrganicLeads($start_date, $end_date)[1];
        $query = ClientLog::onlineOrganicFilteredLeads()->whereBetween('created_at', [$start_date, $end_date])->whereIn('client_id', $unfiltered_leads);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ]; 
    }

    public function filteredOfflineLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $unfiltered_leads = $this->unfilteredOfflineLeads($start_date, $end_date)[1];
        $query = ClientLog::offlineFilteredLeads()->whereBetween('created_at', [$start_date, $end_date])->whereIn('client_id', $unfiltered_leads);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function filteredReferralSales(Carbon $start_date, Carbon $end_date): Array
    {
        $unfiltered_leads = $this->unfilteredReferralLeads($start_date, $end_date)[1];
        $query = ClientLog::referralFromExistingClientsFilteredLeads()->whereBetween('created_at', [$start_date, $end_date])->whereIn('client_id', $unfiltered_leads);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    /**
     * Summary of potentialOnlinePaidLeads
     * @param \Illuminate\Support\Carbon $start_date
     * @param \Illuminate\Support\Carbon $end_date
     * @return int
     */
    public function potentialOnlinePaidLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $filtered_leads = $this->filteredOnlinePaidLeads($start_date, $end_date)[1];
        $query = ClientLog::onlinePaidPotentialLeads()->whereBetween('created_at', [$start_date, $end_date])->whereIn('client_id', $filtered_leads);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function potentialOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $filtered_leads = $this->filteredOnlineOrganicLeads($start_date, $end_date)[1];
        $query = ClientLog::onlineOrganicPotentialLeads()->whereBetween('created_at', [$start_date, $end_date])->whereIn('client_id', $filtered_leads);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function potentialOfflineLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $filtered_leads = $this->filteredOfflineLeads($start_date, $end_date)[1];
        $query = ClientLog::offlinePotentialLeads()->whereBetween('created_at', [$start_date, $end_date])->whereIn('client_id', $filtered_leads);
        return [
            $query->get()->count(),
            $query->pluck('client_id' )->toArray()
        ];
    }

    public function potentialReferralExistingClientLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $filtered_leads = $this->filteredReferralSales($start_date, $end_date)[1];
        $query = ClientLog::referralFromExistingClientsPotentialLeads()->whereBetween('created_at', [$start_date, $end_date])->whereIn('client_id', $filtered_leads);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    /**
     * Deal
     * @param \Illuminate\Support\Carbon $start_date
     * @param \Illuminate\Support\Carbon $end_date
     * @return int
     */
    public function dealOnlinePaidLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::onlinePaidDealLeads($start_date, $end_date)->get()->count();
    }

    public function dealOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::onlineOrganicDealLeads($start_date, $end_date)->get()->count();
    }

    public function dealOfflineLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::offlineDealLeads($start_date, $end_date)->get()->count();
    }

    public function dealReferralExistingClientLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::referralFromExistingClientsDealLeads($start_date, $end_date)->get()->count();
    }

    /**
     * Agreement
     * @param \Illuminate\Support\Carbon $start_date
     * @param \Illuminate\Support\Carbon $end_date
     * @return int
     */
    public function agreementOnlinePaidLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::onlinePaidAgreement()->whereBetween('created_at', [$start_date, $end_date])->get()->count();
    }

    public function agreementOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::onlineOrganicAgreement()->whereBetween('created_at', [$start_date, $end_date])->get()->count();
    }

    public function agreementOfflineLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::offlineAgreement()->whereBetween('created_at', [$start_date, $end_date])->get()->count();
    }

    public function agreementReferralExistingClientLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::referralFromExistingClientsAgreement()->whereBetween('created_at', [$start_date, $end_date])->get()->count();
    }

    /**
     * Payment
     * @param \Illuminate\Support\Carbon $start_date
     * @param \Illuminate\Support\Carbon $end_date
     * @return int
     */
    public function paymentOnlinePaidLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::onlinePaidPaymentLeads($start_date, $end_date)->whereBetween('created_at', [$start_date, $end_date])->get()->count();
    }

    public function paymentOnlineOrganicLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::onlineOrganicPaymentLeads($start_date, $end_date)->whereBetween('created_at', [$start_date, $end_date])->get()->count();
    }
    public function paymentOfflineLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::offlinePaymentLeads($start_date, $end_date)->whereBetween('created_at', [$start_date, $end_date])->get()->count();
    }
    public function paymentReferralExistingClientLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::referralFromExistingClientsPaymentLeads($start_date, $end_date)->whereBetween('created_at', [$start_date, $end_date])->get()->count();
    }

    /**
     *  Data by Product category 
     *  Mentoring
     * 
     *
     * Potential Leads
     */
    public function mentoringOnlinePaidPotentialLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $query = ClientLog::mentoring()->onlinePaid()->potentialLeadsByProduct()->whereBetween('created_at', [$start_date, $end_date]);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function mentoringOnlineOrganicPotentialLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $query = ClientLog::mentoring()->onlineOrganic()->potentialLeadsByProduct()->whereBetween('created_at', [$start_date, $end_date]);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function mentoringOfflinePotentialLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $query = ClientLog::mentoring()->offline()->potentialLeadsByProduct()->whereBetween('created_at', [$start_date, $end_date]);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function mentoringReferralPotentialLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $query = ClientLog::mentoring()->referral()->potentialLeadsByProduct()->whereBetween('created_at', [$start_date, $end_date]);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray(),
        ];
    }

    /**
     *  Assessment Form
     * 
     */
    public function mentoringOnlinePaidAssessmentForm(Carbon $start_date, Carbon $end_date): Array
    {
        $potentials = $this->mentoringOnlinePaidPotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::mentoring()->onlinePaid()->tookAssessment($start_date, $end_date)->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function mentoringOnlineOrganicAssessmentForm(Carbon $start_date, Carbon $end_date): Array
    {
        $potentials = $this->mentoringOnlineOrganicPotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::mentoring()->onlineOrganic()->tookAssessment($start_date, $end_date)->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function mentoringOfflineAssessmentForm(Carbon $start_date, Carbon $end_date): Array
    {
        $potentials = $this->mentoringOfflinePotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::mentoring()->offline()->tookAssessment($start_date, $end_date)->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function mentoringReferralAssessmentForm(Carbon $start_date, Carbon $end_date): Array
    {
        $potentials = $this->mentoringReferralPotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::mentoring()->referral()->tookAssessment($start_date, $end_date)->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    /**
     * Initial Consult (IC)
     */
    //! perlu where IN potential utk assessment, ic, ia
    public function mentoringOnlinePaidIC(Carbon $start_date, Carbon $end_date): Array
    {
        $potentials = $this->mentoringOnlinePaidPotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::mentoring()->onlinePaid()->initialConsult()->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }
    public function mentoringOnlineOrganicIC(Carbon $start_date, Carbon $end_date): Array
    {
        $potentials = $this->mentoringOnlineOrganicPotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::mentoring()->onlineOrganic()->initialConsult()->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }
    public function mentoringOfflineIC(Carbon $start_date, Carbon $end_date): Array
    {
        $potentials = $this->mentoringOfflinePotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::mentoring()->offline()->initialConsult()->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }
    public function mentoringReferralIC(Carbon $start_date, Carbon $end_date): Array
    {
        $potentials = $this->mentoringReferralPotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::mentoring()->referral()->initialConsult()->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    /**
     * Initial Assessment (IAR/IA)
     */
    public function mentoringOnlinePaidIA(Carbon $start_date, Carbon $end_date): Int
    {
        $potentials = $this->mentoringOnlinePaidPotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::mentoring()->onlinePaid()->initialAssessment()->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return $query->get()->count();
    }
    public function mentoringOnlineOrganicIA(Carbon $start_date, Carbon $end_date): Int
    {
        $potentials = $this->mentoringOnlineOrganicPotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::mentoring()->onlineOrganic()->initialAssessment()->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return $query->get()->count();
    }
    public function mentoringOfflineIA(Carbon $start_date, Carbon $end_date): Int
    {
        $potentials = $this->mentoringOfflinePotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::mentoring()->offline()->initialAssessment()->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return $query->get()->count();
    }
    public function mentoringReferralIA(Carbon $start_date, Carbon $end_date): Int
    {
        $potentials = $this->mentoringReferralPotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::mentoring()->referral()->initialAssessment()->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return $query->get()->count();
    }

    /**
     * Deal
     */
    public function mentoringOnlinePaidDeal(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::mentoring()->onlinePaid()->dealLeads($start_date, $end_date)->get()->count();
    }

    public function mentoringOnlineOrganicDeal(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::mentoring()->onlineOrganic()->dealLeads($start_date, $end_date)->get()->count();
    }
    
    public function mentoringOfflineDeal(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::mentoring()->offline()->dealLeads($start_date, $end_date)->get()->count();
    }
    
    public function mentoringReferralDeal(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::mentoring()->referral()->dealLeads($start_date, $end_date)->get()->count();
    }
    

    /**
     * Agreement
     * @return void
     */
    public function mentoringOnlinePaidAgreement(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::mentoring()->onlinePaid()->hasAgreement($start_date, $end_date)->get()->count();
    }
    
    public function mentoringOnlineOrganicAgreement(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::mentoring()->onlineOrganic()->hasAgreement($start_date, $end_date)->get()->count();
    }
    
    public function mentoringOfflineAgreement(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::mentoring()->offline()->hasAgreement($start_date, $end_date)->get()->count();
    }
    
    public function mentoringReferralAgreement(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::mentoring()->referral()->hasAgreement($start_date, $end_date)->get()->count();
    }
    

    /**
     * Payment
     */
    public function mentoringOnlinePaidPayment(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::mentoring()->onlinePaid()->alreadyPaidTheProgram($start_date, $end_date)->get()->count();
    }

    public function mentoringOnlineOrganicPayment(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::mentoring()->onlineOrganic()->alreadyPaidTheProgram($start_date, $end_date)->get()->count();
    }

    public function mentoringOfflinePayment(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::mentoring()->offline()->alreadyPaidTheProgram($start_date, $end_date)->get()->count();
    }

    public function mentoringReferralPayment(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::mentoring()->referral()->alreadyPaidTheProgram($start_date, $end_date)->get()->count();
    }



    /**
     * 
     *  Data by Sales
     *  Mentoring
     * 
     * 
     */
    public function mentoringPotentialLeadsTotalToDate(Carbon $start_date, Carbon $end_date)
    {
        $query = ClientLog::with([
                'client_program' => function ($query) {
                    $query->with([
                        'internalPic' => function ($sub) {
                            $sub->select('id', 'first_name', 'last_name');
                        },
                    ])->select('clientprog_id', 'client_id', 'empl_id');
                },
            ])->mentoring()->potentialLeadsByProduct()->whereBetween('created_at', [$start_date, $end_date]);
        $potentials = $query->get();

        $mapped = $potentials->map(function ($item) {
            return [
                'pic_id' => $item->client_program->internalPic->id,
                'pic_name' => $item->client_program->internalPic->full_name,
                'clientprogram_id' => $item->client_program->clientprog_id,
                'client_id' => $item->client_id,
                'category' => $item->category,
                'unique_key' => $item->unique_key,
                'lead_source' => $item->lead_source,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        return [
            $query->pluck('client_id')->toArray(),
            $mapped->groupBy('pic_id'),
        ];
    }
    

    /**
     * Assessment Form
     */
    public function mentoringAssessmentFormTotalToDate($potentials, Carbon $start_date, Carbon $end_date)
    {
        $query = ClientLog::with([
                'client_program' => function ($query) {
                    $query->with([
                        'internalPic' => function ($sub) {
                            $sub->select('id', 'first_name', 'last_name');
                        },
                    ])->select('clientprog_id', 'client_id', 'empl_id');
                },
            ])->mentoring()->potentialLeadsByProduct()->whereHas('master_client', function ($query) use ($start_date, $end_date) {
                $query->where('took_ia', 1)->whereBetween('took_ia_date', [$start_date, $end_date]);
            })->whereIn('client_id', $potentials);
        $assessment = $query->get();

        $mapped = $assessment->map(function ($item) {
            return [
                'pic_id' => $item->client_program->internalPic->id,
                'pic_name' => $item->client_program->internalPic->full_name,
                'clientprogram_id' => $item->client_program->clientprog_id,
                'client_id' => $item->client_id,
                'category' => $item->category,
                'unique_key' => $item->unique_key,
                'lead_source' => $item->lead_source,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        return [
            $query->pluck('client_id')->toArray(),
            $mapped->groupBy('pic_id')
        ];
    }
    

    /**
     * Initial Consult
     */
    public function mentoringICTotalToDate($potentials, Carbon $start_date, Carbon $end_date)
    {
        $query = ClientLog::with([
                'client_program' => function ($query)  {
                    $query->with([
                        'internalPic' => function ($sub) {
                            $sub->select('id', 'first_name', 'last_name');
                        },
                    ])->select('clientprog_id', 'client_id', 'empl_id');
                },
            ])->mentoring()->potentialLeadsByProduct()->whereIn('client_id', $potentials)->whereHas('client_program', function ($sub) use ($start_date, $end_date) {
                $sub->whereBetween('initconsult_date', [$start_date, $end_date]);
            });

        $ic = $query->get();

        $mapped = $ic->map(function ($item) {
            return [
                'pic_id' => $item->client_program->internalPic->id,
                'pic_name' => $item->client_program->internalPic->full_name,
                'clientprogram_id' => $item->client_program->clientprog_id,
                'client_id' => $item->client_id,
                'category' => $item->category,
                'unique_key' => $item->unique_key,
                'lead_source' => $item->lead_source,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        return [
            $query->pluck('client_id')->toArray(),
            $mapped->groupBy('pic_id')
        ];
    }
    

    /**
     * Initial Assessment Request IAR / IA
     */
    public function mentoringIATotalToDate($potentials, Carbon $start_date, Carbon $end_date)
    {
        $ic = ClientLog::with([
            'client_program' => function ($query)  use ($start_date, $end_date) {
                $query->with([
                    'internalPic' => function ($sub) {
                        $sub->select('id', 'first_name', 'last_name');
                    },
                ])->select('clientprog_id', 'client_id', 'empl_id');
            },
        ])->mentoring()->potentialLeadsByProduct()->whereIn('client_id', $potentials)->whereHas('client_program', function ($sub) use ($start_date, $end_date){
            $sub->whereBetween('assessmentsent_date', [$start_date, $end_date]);
        })->get();

        $mapped = $ic->map(function ($item) {
            return [
                'pic_id' => $item->client_program->internalPic->id,
                'pic_name' => $item->client_program->internalPic->full_name,
                'clientprogram_id' => $item->client_program->clientprog_id,
                'client_id' => $item->client_id,
                'category' => $item->category,
                'unique_key' => $item->unique_key,
                'lead_source' => $item->lead_source,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        return $mapped->groupBy('pic_id');
    }
    

    /**
     * Deal
     */
    public function mentoringDealTotalToDate(Carbon $start_date, Carbon $end_date)
    {
        $deal = ClientProgram::whereHas('program.main_prog', function ($query) {
                $query->where('prog_name', 'Admissions Mentoring');
            })->
            whereIn('status', [1, 4])->
            whereBetween('success_date', [$start_date, $end_date])->
            success()->
            get();
        $mapped = $deal->map(function ($item) {
            return [
                'pic_id' => $item->internalPic->id,
                'pic_name' => $item->internalPic->full_name,
                'clientprogram_id' => $item->clientprog_id
            ];
        });

        return $mapped->groupBy('pic_id');
    }
    

    /**
     * Agreement
     */
    public function mentoringAgreementTotalToDate(Carbon $start_date, Carbon $end_date)
    {
        $agreement = ClientProgram::whereHas('program.main_prog', function ($query) {
                $query->where('prog_name', 'Admissions Mentoring');
            })->whereBetween('agreement_uploaded_at', [$start_date, $end_date])->get();
        $mapped = $agreement->map(function ($item) {
            return [
                'pic_id' => $item->internalPic->id,
                'pic_name' => $item->internalPic->full_name,
                'clientprogram_id' => $item->clientprog_id
            ];
        });

        return $mapped->groupBy('pic_id');
    }
    

    /**
     * Payment
     */
    public function mentoringPaymentTotalToDate(Carbon $start_date, Carbon $end_date)
    {
        $payment = ClientProgram::with([
                'invoice.firstReceipt'
            ])->
            whereHas('program.main_prog', function ($query) {
                $query->where('prog_name', 'Admissions Mentoring');
            })->
            whereHas('invoice.firstReceipt', function ($query) use ($start_date, $end_date) {
                $query->whereNotNull('receipt_date')->whereBetween('receipt_date', [$start_date, $end_date]);
            })->
            where('status', 1)->get();
        $mapped = $payment->map(function ($item) {
            return [
                'pic_id' => $item->internalPic->id,
                'pic_name' => $item->internalPic->full_name,
                'clientprogram_id' => $item->clientprog_id,
            ];
        });

        return $mapped->groupBy('pic_id');
    }

    /**
     * leads of tutoring
     * @return int
     */
    public function tutoringOnlinePaidPotentialLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $query = ClientLog::tutoring()->onlinePaid()->potentialLeadsByProduct()->whereBetween('created_at', [$start_date, $end_date]);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function tutoringOnlineOrganicPotentialLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $query = ClientLog::tutoring()->onlineOrganic()->potentialLeadsByProduct()->whereBetween('created_at', [$start_date, $end_date]);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function tutoringOfflinePotentialLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $query = ClientLog::tutoring()->offline()->potentialLeadsByProduct()->whereBetween('created_at', [$start_date, $end_date]);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function tutoringReferralPotentialLeads(Carbon $start_date, Carbon $end_date): Array
    {
        $query = ClientLog::tutoring()->referral()->potentialLeadsByProduct()->whereBetween('created_at', [$start_date, $end_date]);
        return [
            $query->get()->count(),
            $query->pluck('client_id')->toArray()
        ];
    }

    public function tutoringOnlinePaidTrialDate(Carbon $start_date, Carbon $end_date)
    {
        $potentials = $this->tutoringOnlinePaidPotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::tutoring()->onlinePaid()->trialDate($start_date, $end_date)->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return $query->get()->count();
    }

    public function tutoringOnlineOrganicTrialDate(Carbon $start_date, Carbon $end_date)
    {
        $potentials = $this->tutoringOnlineOrganicPotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::tutoring()->onlineOrganic()->trialDate($start_date, $end_date)->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return $query->get()->count();
    }

    public function tutoringOfflineTrialDate(Carbon $start_date, Carbon $end_date)
    {
        $potentials = $this->tutoringOfflinePotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::tutoring()->offline()->trialDate($start_date, $end_date)->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return $query->get()->count();
    }

    public function tutoringReferralTrialDate(Carbon $start_date, Carbon $end_date)
    {
        $potentials = $this->tutoringReferralPotentialLeads($start_date, $end_date)[1];
        $query = ClientLog::tutoring()->referral()->trialDate($start_date, $end_date)->whereIn('client_id', $potentials)->groupBy('clientprog_id');
        return $query->get()->count();
    }

    public function tutoringOnlinePaidDeal(Carbon $start_date, Carbon $end_date)
    {
        return ClientProgram::tutoring()->onlinePaid()->dealLeads($start_date, $end_date)->get()->count();
    }

    public function tutoringOnlineOrganicDeal(Carbon $start_date, Carbon $end_date)
    {
        return ClientProgram::tutoring()->onlineOrganic()->dealLeads($start_date, $end_date)->get()->count();
    }

    public function tutoringOfflineDeal(Carbon $start_date, Carbon $end_date)
    {
        return ClientProgram::tutoring()->offline()->dealLeads($start_date, $end_date)->get()->count();
    }

    public function tutoringReferralDeal(Carbon $start_date, Carbon $end_date)
    {
        return ClientProgram::tutoring()->referral()->dealLeads($start_date, $end_date)->get()->count();
    }

    public function tutoringOnlinePaidPayment(Carbon $start_date, Carbon $end_date)
    {
        return ClientProgram::tutoring()->onlinePaid()->alreadyPaidTheProgram($start_date, $end_date)->get()->count();
    }

    public function tutoringOnlineOrganicPayment(Carbon $start_date, Carbon $end_date)
    {
        return ClientProgram::tutoring()->onlineOrganic()->alreadyPaidTheProgram($start_date, $end_date)->get()->count();
    }

    public function tutoringOfflinePayment(Carbon $start_date, Carbon $end_date)
    {
        return ClientProgram::tutoring()->offline()->alreadyPaidTheProgram($start_date, $end_date)->get()->count();
    }

    public function tutoringReferralPayment(Carbon $start_date, Carbon $end_date)
    {
        return ClientProgram::tutoring()->referral()->alreadyPaidTheProgram($start_date, $end_date)->get()->count();
    }

    /**
     * 
     *  Data by Sales
     *  Tutoring
     * 
     * 
     */
    public function tutoringPotentialLeadsTotalToDate(Carbon $start_date, Carbon $end_date)
    {
        $query = ClientLog::with([
            'client_program' => function ($query) {
                $query->with([
                    'internalPic' => function ($sub) {
                        $sub->select('id', 'first_name', 'last_name');
                    },
                ])->select('clientprog_id', 'client_id', 'empl_id');
            },
        ])->tutoring()->potentialLeadsByProduct()->whereBetween('created_at', [$start_date, $end_date]);
        $potentials = $query->get();

        $mapped = $potentials->map(function ($item) {
            return [
                'pic_id' => $item->client_program->internalPic->id,
                'pic_name' => $item->client_program->internalPic->full_name,
                'clientprogram_id' => $item->client_program->clientprog_id,
                'client_id' => $item->client_id,
                'category' => $item->category,
                'unique_key' => $item->unique_key,
                'lead_source' => $item->lead_source,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        return [
            $query->pluck('client_id')->toArray(),
            $mapped->groupBy('pic_id'),
        ];
    }

    public function tutoringTrialDateTotalToDate($potentials, Carbon $start_date, Carbon $end_date)
    {
        $trial_date = ClientLog::with([
            'client_program' => function ($query) {
                $query->with([
                    'internalPic' => function ($sub) {
                        $sub->select('id', 'first_name', 'last_name');
                    },
                ])->select('clientprog_id', 'client_id', 'empl_id');
            },
        ])->tutoring()->potentialLeadsByProduct()->trialDate($start_date, $end_date)->whereIn('client_id', $potentials)->get();

        $mapped = $trial_date->map(function ($item) {
            return [
                'pic_id' => $item->client_program->internalPic->id,
                'pic_name' => $item->client_program->internalPic->full_name,
                'clientprogram_id' => $item->client_program->clientprog_id,
                'client_id' => $item->client_id,
                'category' => $item->category,
                'unique_key' => $item->unique_key,
                'lead_source' => $item->lead_source,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        return $mapped->groupBy('pic_id');
    }

    public function tutoringDealTotalToDate(Carbon $start_date, Carbon $end_date)
    {
        $deal = ClientProgram::whereHas('program.main_prog', function ($query) {
            $query->where('prog_name', 'Academic & Test Preparation');
        })->
        whereBetween('success_date', [$start_date, $end_date])->
        success()->
        get();
        $mapped = $deal->map(function ($item) {
            return [
                'pic_id' => $item->internalPic->id,
                'pic_name' => $item->internalPic->full_name,
                'clientprogram_id' => $item->clientprog_id
            ];
        });

        return $mapped->groupBy('pic_id');
    }

    public function tutoringPaymentTotalToDate(Carbon $start_date, Carbon $end_date)
    {
        $payment = ClientProgram::with([
            'invoice.firstReceipt'
        ])->
        whereHas('program.main_prog', function ($query) {
            $query->where('prog_name', 'Academic & Test Preparation');
        })->
        whereHas('invoice.firstReceipt', function ($query) use ($start_date, $end_date) {
            $query->whereNotNull('receipt_date')->whereBetween('receipt_date', [$start_date, $end_date]);
        })->
        where('status', 1)->get();
        $mapped = $payment->map(function ($item) {
            return [
                'pic_id' => $item->internalPic->id,
                'pic_name' => $item->internalPic->full_name,
                'clientprogram_id' => $item->clientprog_id,
            ];
        });

        return $mapped->groupBy('pic_id');
    }
    
    /**
     * leads of gip
     * @return int
     */
    public function gipOnlinePaidPotentialLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::GIP()->onlinePaid()->potentialLeadsByProduct()->whereBetween('created_at', [$start_date, $end_date])->get()->count();
    }

    public function gipOnlineOrganicPotentialLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::GIP()->onlineOrganic()->potentialLeadsByProduct()->whereBetween('created_at', [$start_date, $end_date])->get()->count();
    }

    public function gipOfflinePotentialLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::GIP()->offline()->potentialLeadsByProduct()->whereBetween('created_at', [$start_date, $end_date])->get()->count();
    }

    public function gipReferralPotentialLeads(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientLog::GIP()->referral()->potentialLeadsByProduct()->whereBetween('created_at', [$start_date, $end_date])->get()->count();
    }

    public function gipOnlinePaidDeal(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::GIP()->onlinePaid()->dealLeads($start_date, $end_date)->get()->count();
    }

    public function gipOnlineOrganicDeal(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::GIP()->onlineOrganic()->dealLeads($start_date, $end_date)->get()->count();
    }

    public function gipOfflineDeal(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::GIP()->offline()->dealLeads($start_date, $end_date)->get()->count();
    }

    public function gipReferralDeal(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::GIP()->referral()->dealLeads($start_date, $end_date)->get()->count();
    }

    public function gipOnlinePaidPayment(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::GIP()->onlinePaid()->alreadyPaidTheProgram($start_date, $end_date)->get()->count();
    }
    public function gipOnlineOrganicPayment(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::GIP()->onlineOrganic()->alreadyPaidTheProgram($start_date, $end_date)->get()->count();
    }
    public function gipOfflinePayment(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::GIP()->offline()->alreadyPaidTheProgram($start_date, $end_date)->get()->count();
    }
    public function gipReferralPayment(Carbon $start_date, Carbon $end_date): Int
    {
        return ClientProgram::GIP()->referral()->alreadyPaidTheProgram($start_date, $end_date)->get()->count();
    }

    /**
     * 
     *  Data by Sales
     *  Tutoring
     * 
     * 
     */
    public function gipPotentialLeadsTotalToDate(Carbon $start_date, Carbon $end_date)
    {
        $potentials = ClientLog::with([
            'client_program' => function ($query) {
                $query->with([
                    'internalPic' => function ($sub) {
                        $sub->select('id', 'first_name', 'last_name');
                    },
                ])->select('clientprog_id', 'client_id', 'empl_id');
            },
        ])->GIP()->potentialLeadsByProduct()->whereBetween('created_at', [$start_date, $end_date])->get();

        $mapped = $potentials->map(function ($item) {
            return [
                'pic_id' => $item->client_program->internalPic->id ?? NULL,
                'pic_name' => $item->client_program->internalPic->full_name ?? NULL,
                'clientprogram_id' => $item->client_program->clientprog_id,
                'client_id' => $item->client_id,
                'category' => $item->category,
                'unique_key' => $item->unique_key,
                'lead_source' => $item->lead_source,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        });

        return $mapped->groupBy('pic_id');
    }

    public function gipDealTotalToDate(Carbon $start_date, Carbon $end_date)
    {
        $deal = ClientProgram::whereHas('program.sub_prog', function ($query) {
            $query->where('sub_prog_name', 'Global Immersion Program');
        })->
        whereBetween('success_date', [$start_date, $end_date])->
        success()->
        get();
        $mapped = $deal->map(function ($item) {
            return [
                'pic_id' => $item->internalPic->id,
                'pic_name' => $item->internalPic->full_name,
                'clientprogram_id' => $item->clientprog_id
            ];
        });

        return $mapped->groupBy('pic_id');
    }

    public function gipPaymentTotalToDate(Carbon $start_date, Carbon $end_date)
    {
        $payment = ClientProgram::with([
            'invoice.firstReceipt'
        ])->
        whereHas('program.sub_prog', function ($query) {
            $query->where('sub_prog_name', 'Global Immersion Program');
        })->
        whereHas('invoice.firstReceipt', function ($query) use ($start_date, $end_date) {
            $query->whereNotNull('receipt_date')->whereBetween('receipt_date', [$start_date, $end_date]);
        })->
        where('status', 1)->get();
        $mapped = $payment->map(function ($item) {
            return [
                'pic_id' => $item->internalPic->id,
                'pic_name' => $item->internalPic->full_name,
                'clientprogram_id' => $item->clientprog_id,
            ];
        });

        return $mapped->groupBy('pic_id');
    }

}
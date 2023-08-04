<?php

namespace App\Repositories;

use App\Interfaces\LeadTargetRepositoryInterface;
use App\Models\ClientProgram;
use App\Models\LeadTargetTracking;
use App\Models\UserClient;
use App\Models\ViewClientProgram;
use App\Models\ViewTargetSignal;

class LeadTargetRepository implements LeadTargetRepositoryInterface
{
    public function getThisMonthTarget()
    {
        return ViewTargetSignal::all();
    }

    public function findThisMonthTarget($now)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        return LeadTargetTracking::whereMonth('month_year', $month)->whereYear('month_year', $year)->get();
    }

    public function findThisMonthTargetByDivision($now, $divisi)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        return LeadTargetTracking::whereMonth('month_year', $month)->whereYear('month_year', $year)->where('divisi', $divisi)->first();
    }

    public function getIncompleteTargetFromLastMonthByDivision($now, $divisi)
    {
        $last_month = date('m', strtotime('-1 month', strtotime($now)));
        $last_year = date('Y', strtotime('-1 month', strtotime($now)));
        
        return LeadTargetTracking::whereMonth('month_year', $last_month)->whereYear('month_year', $last_year)->where('divisi', $divisi)->first();
    }

    public function updateActualLead($details, $now, $divisi)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        return LeadTargetTracking::
                whereMonth('month_year', $month)->
                whereYear('month_year', $year)->
                where('divisi', $divisi)->
                update($details);
    }

    public function getAchievedLeadSalesByMonth($now)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        return UserClient::
                    whereHas('lead', function ($query) {
                        $query->where('note', 'Sales')->where('main_lead', '!=', 'Referral');    
                    })->
                    whereHas('leadStatus', function ($query) use ($month, $year) {
                        $query->
                            whereMonth('tbl_client_lead_tracking.updated_at', $month)->
                            whereYear('tbl_client_lead_tracking.updated_at', $year);
                    })->
                    get();
    }

    public function getAchievedHotLeadSalesByMonth($now)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        return UserClient::
                    whereHas('lead', function ($query) {
                        $query->where('note', 'Sales')->where('main_lead', '!=', 'Referral');    
                    })->
                    whereHas('leadStatus', function ($query) use ($month, $year) {
                        $query->
                            whereMonth('tbl_client_lead_tracking.updated_at', $month)->
                            whereYear('tbl_client_lead_tracking.updated_at', $year)->
                            where('tbl_initial_program_lead.name', 'Admissions Mentoring')->
                            where('tbl_client_lead_tracking.type', 'Lead')->
                            where('tbl_client_lead_tracking.total_result', '>=', 0.65); # >= 0.65 means HOT
                    })->
                    get();
    }

    public function getAchievedInitConsultSalesByMonth($now)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        return UserClient::
                    whereHas('lead', function ($query) {
                        $query->where('note', 'Sales')->where('main_lead', '!=', 'Referral');    
                    })->
                    // whereHas('leadStatus', function ($query) use ($month, $year) {
                    //     $query->
                    //         whereMonth('tbl_client_lead_tracking.updated_at', $month)->
                    //         whereYear('tbl_client_lead_tracking.updated_at', $year);
                    // })->
                    whereHas('clientProgram', function ($query) use ($month, $year) {
                        $query->
                            whereMonth('assessmentsent_date', $month)->
                            whereYear('assessmentsent_date', $year);
                    })->
                    get();
    }

    public function getAchievedContributionSalesByMonth($now)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        return UserClient::
                    whereHas('lead', function ($query) {
                        $query->where('note', 'Sales')->where('main_lead', '!=', 'Referral');    
                    })->
                    whereHas('clientProgram', function ($query) use ($month, $year) {
                        $query->
                            whereMonth('updated_at', $month)->
                            whereYear('updated_at', $year)->
                            where('status', 1)-> # status programnya success
                            whereHas('invoice', function ($subQuery) {
                                $subQuery->
                                    where('inv_status', '!=', 2); # status invoicenya tidak refund
                            });
                    })->
                    get();
    }

    public function getAchievedLeadReferralByMonth($now)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        # clients where source lead is referral
        $clients = UserClient::
                    whereHas('lead', function ($query) {
                        $query->where('main_lead', 'Referral');    
                    })->
                    whereHas('leadStatus', function ($query) use ($month, $year) {
                        $query->
                            whereMonth('tbl_client_lead_tracking.updated_at', $month)->
                            whereYear('tbl_client_lead_tracking.updated_at', $year);
                    })->
                    get();

        $client_program = ClientProgram::
                    leftJoin('tbl_client as c', 'c.id', '=', 'tbl_client_prog.client_id')->
                    whereHas('lead', function ($query) {
                        $query->where('main_lead', 'Referral');
                    })->
                    where('status', 0)->
                    whereMonth('tbl_client_prog.created_at', $month)->
                    whereYear('tbl_client_prog.created_at', $year)->
                    get('c.*');

        $achieved_lead = $clients->merge($client_program);

        return $achieved_lead;
    }

    public function getAchievedHotLeadReferralByMonth($now)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        return UserClient::
                    whereHas('lead', function ($query) {
                        $query->where('main_lead', 'Referral');    
                    })->
                    whereHas('leadStatus', function ($query) use ($month, $year) {
                        $query->
                            whereMonth('tbl_client_lead_tracking.updated_at', $month)->
                            whereYear('tbl_client_lead_tracking.updated_at', $year)->
                            where('tbl_initial_program_lead.name', 'Admissions Mentoring')->
                            where('tbl_client_lead_tracking.type', 'Lead')->
                            where('tbl_client_lead_tracking.total_result', '>=', 0.65); # >= 0.65 means HOT & we need to get only the hot leads
                    })->
                    get();
    }
}

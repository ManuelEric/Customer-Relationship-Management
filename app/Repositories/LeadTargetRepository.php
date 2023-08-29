<?php

namespace App\Repositories;

use App\Interfaces\LeadTargetRepositoryInterface;
use App\Models\Client;
use App\Models\ClientProgram;
use App\Models\Invb2b;
use App\Models\InvoiceProgram;
use App\Models\LeadTargetTracking;
use App\Models\Receipt;
use App\Models\UserClient;
use App\Models\ViewClientProgram;
use App\Models\ViewTargetSignal;
use Illuminate\Support\Facades\DB;

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
                    // whereHas('leadStatus', function ($query) use ($month, $year) {
                    //     $query->
                    //         whereMonth('tbl_client_lead_tracking.updated_at', $month)->
                    //         whereYear('tbl_client_lead_tracking.updated_at', $year);
                    // })->
                    whereHas('clientProgram', function ($query) use ($month, $year) {
                        $query->
                            whereMonth('assessmentsent_date', $month)->
                            whereYear('assessmentsent_date', $year)->
                            whereHas('lead', function ($query) {
                                $query->where('note', 'Sales')->where('main_lead', '!=', 'Referral');    
                            })->
                            whereHas('program', function ($subQuery) {
                                $subQuery->whereHas('main_prog', function ($subQuery2) {
                                    $subQuery2->where('prog_name', 'like', '%Admissions Mentoring%');
                                })->whereHas('sub_prog', function ($subQuery2) {
                                    $subQuery2->where('sub_prog_name', 'like', '%Admissions Mentoring%');
                                });
                            });
                    })->
                    get();
    }

    public function getAchievedContributionSalesByMonth($now)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        return UserClient::
                    whereHas('clientProgram', function ($query) use ($month, $year) {
                        $query->where(function ($subQuery) use ($month, $year){
                            $subQuery->whereMonth('success_date', $month)->
                                whereYear('success_date', $year);
                            })->orWhere(function ($subQuery) use ($month, $year){
                                $subQuery->whereMonth('assessmentsent_date', $month)->
                                whereYear('assessmentsent_date', $year);
                            })->
                            where('status', 1)-> # status programnya success
                            whereHas('lead', function ($query) {
                                $query->where('note', 'Sales')->where('main_lead', '!=', 'Referral');    
                            })->
                            whereHas('program', function ($subQuery) {
                                $subQuery->whereHas('main_prog', function ($subQuery2) {
                                    $subQuery2->where('prog_name', 'like', '%Admissions Mentoring%');
                                })->whereHas('sub_prog', function ($subQuery2) {
                                    $subQuery2->where('sub_prog_name', 'like', '%Admissions Mentoring%');
                                });
                            });
                            // ->
                            // whereHas('invoice', function ($subQuery) {
                            //     $subQuery->
                            //         where('inv_status', '!=', 2); # status invoicenya tidak refund
                            // });
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

    public function getAchievedInitConsultReferralByMonth($now)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        # get all client from referral and client program from referral
        return UserClient::
                    // where(function ($query) {
                    //     $query->whereHas('lead', function ($subQuery) {
                    //         $subQuery->where('main_lead', 'Referral');
                    //     })->
                    //     orWhereHas('clientProgram', function ($subQuery) {
                    //         $subQuery->whereHas('lead', function ($subQuery_2) {
                    //             $subQuery_2->where('main_lead', 'Referral');
                    //         });
                    //     });
                    // })->
                    whereHas('clientProgram', function ($query) use ($month, $year) {
                        $query->
                            whereMonth('assessmentsent_date', $month)->
                            whereYear('assessmentsent_date', $year)->
                            whereHas('lead', function ($query) {
                                $query->where('main_lead', 'Referral');    
                            })->
                            whereHas('program', function ($subQuery) {
                                $subQuery->whereHas('main_prog', function ($subQuery2) {
                                    $subQuery2->where('prog_name', 'like', '%Admissions Mentoring%');
                                })->whereHas('sub_prog', function ($subQuery2) {
                                    $subQuery2->where('sub_prog_name', 'like', '%Admissions Mentoring%');
                                });
                            });
                    })->
                    get();
    }

    public function getAchievedContributionReferralByMonth($now)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        return UserClient::
                    // where(function ($query) {
                    //     $query->whereHas('lead', function ($subQuery) {
                    //         $subQuery->where('main_lead', 'Referral');
                    //     })->
                    //     orWhereHas('clientProgram', function ($subQuery) {
                    //         $subQuery->whereHas('lead', function ($subQuery_2) {
                    //             $subQuery_2->where('main_lead', 'Referral');
                    //         });
                    //     });
                    // })->
                    whereHas('clientProgram', function ($query) use ($month, $year) {
                        $query->where(function ($subQuery) use ($month, $year){
                                $subQuery->whereMonth('success_date', $month)->
                                whereYear('success_date', $year);
                            })->orWhere(function ($subQuery) use ($month, $year){
                                $subQuery->whereMonth('assessmentsent_date', $month)->
                                whereYear('assessmentsent_date', $year);
                            })->
                            where('status', 1)-> # status programnya success
                            whereHas('lead', function ($query) {
                                $query->where('main_lead', 'Referral');    
                            })->
                            whereHas('program', function ($subQuery) {
                                $subQuery->whereHas('main_prog', function ($subQuery2) {
                                    $subQuery2->where('prog_name', 'like', '%Admissions Mentoring%');
                                })->whereHas('sub_prog', function ($subQuery2) {
                                    $subQuery2->where('sub_prog_name', 'like', '%Admissions Mentoring%');
                                });
                            });
                            // ->
                            // whereHas('invoice', function ($subQuery) {
                            //     $subQuery->
                            //         where('inv_status', '!=', 2); # status invoicenya tidak refund
                            // });
                    })->
                    get();
    }

    public function getAchievedLeadDigitalByMonth($now)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        return UserClient::
                    whereHas('lead', function ($query) {
                        $query->where('note', 'Digital');    
                    })->
                    whereHas('leadStatus', function ($query) use ($month, $year) {
                        $query->
                            whereMonth('tbl_client_lead_tracking.updated_at', $month)->
                            whereYear('tbl_client_lead_tracking.updated_at', $year);
                    })->
                    get();
    }

    public function getAchievedHotLeadDigitalByMonth($now)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        return UserClient::
                    whereHas('lead', function ($query) {
                        $query->where('note', 'Digital');    
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

    public function getAchievedInitConsultDigitalByMonth($now)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        return UserClient::
                    whereHas('clientProgram', function ($query) use ($month, $year) {
                        $query->
                            whereMonth('assessmentsent_date', $month)->
                            whereYear('assessmentsent_date', $year)->
                            whereHas('lead', function ($query) {
                                $query->where('note', 'Digital');    
                            })->
                            whereHas('program', function ($subQuery) {
                                $subQuery->whereHas('main_prog', function ($subQuery2) {
                                    $subQuery2->where('prog_name', 'like', '%Admissions Mentoring%');
                                })->whereHas('sub_prog', function ($subQuery2) {
                                    $subQuery2->where('sub_prog_name', 'like', '%Admissions Mentoring%');
                                });
                            });
                    })->
                    get();
    }

    public function getAchievedContributionDigitalByMonth($now)
    {
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        return UserClient::
                    whereHas('clientProgram', function ($query) use ($month, $year) {
                        $query->where(function ($subQuery) use ($month, $year){
                            $subQuery->whereMonth('success_date', $month)->
                                whereYear('success_date', $year);
                            })->orWhere(function ($subQuery) use ($month, $year){
                                $subQuery->whereMonth('assessmentsent_date', $month)->
                                whereYear('assessmentsent_date', $year);
                            })->
                            where('status', 1)-> # status programnya success
                            whereHas('lead', function ($query) {
                                $query->where('note', 'Digital');    
                            })->
                            whereHas('program', function ($subQuery) {
                                $subQuery->whereHas('main_prog', function ($subQuery2) {
                                    $subQuery2->where('prog_name', 'like', '%Admissions Mentoring%');
                                })->whereHas('sub_prog', function ($subQuery2) {
                                    $subQuery2->where('sub_prog_name', 'like', '%Admissions Mentoring%');
                                });
                            });
                            // ->
                            // whereHas('invoice', function ($subQuery) {
                            //     $subQuery->
                            //         where('inv_status', '!=', 2); # status invoicenya tidak refund
                            // });
                    })->
                    get();
    }

    
    public function getAchievedRevenue($monthyear)
    {
        
        $year = date('Y', strtotime($monthyear));
        $month = date('m', strtotime($monthyear));

        $invb2c = InvoiceProgram::leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')
                                    ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
                                    ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
                                    ->select(DB::raw('SUM(inv_totalprice_idr) as total'))
                                    ->where('tbl_main_prog.id', 1)
                                    ->whereYear('tbl_inv.created_at', '=', $year)
                                    ->whereMonth('tbl_inv.created_at', '=', $month)
                                    ->get();

        return $invb2c->sum('total');
    }

    public function getLeadDigital($monthYear, $prog_id = null)
    {
        $month = date('m', strtotime($monthYear));
        $year = date('Y', strtotime($monthYear));

        $query = ViewClientProgram::whereMonth('success_date', $month)->whereYear('success_date', $year)->where('lead_from', 'Digital');
        if ($prog_id != null){
            $query->where('prog_id', $prog_id);
        }
        return $query->get();
    }

//     public function getLeadSourceDigital($monthYear)
//     {
//         $clients = $this->getAchievedLeadDigitalByMonth($monthYear);
//         return Client::whereIn('id', $clients->pluck('id'))->get();
//     }

//     public function getConversionLeadDigital($monthYear)
//     {
//         $month = date('m', strtotime($monthYear));
//         $year = date('Y', strtotime($monthYear));

//         $clients = $this->getAchievedInitConsultDigitalByMonth($monthYear);
//         return ViewClientProgram::whereIn('client_id', $clients->pluck('id'))->whereMonth('assessmentsent_date', $month)->whereYear('assessmentsent_date', $year)->get();
// ;

//     }
}
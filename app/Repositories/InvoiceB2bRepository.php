<?php

namespace App\Repositories;

use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Models\Invb2b;
use App\Models\PartnerProg;
use App\Models\Referral;
use App\Models\SchoolProgram;
use App\Models\v1\InvoiceSchool as V1InvoiceSchool;
use Carbon\Carbon;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class InvoiceB2bRepository implements InvoiceB2bRepositoryInterface
{

    // School Program
    public function getAllInvoiceNeededSchDataTables()
    {
        return datatables::eloquent(
            SchoolProgram::leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_sch_prog.sch_id')
                ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_sch_prog.prog_id')
                ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                ->leftJoin('users', 'users.id', '=', 'tbl_sch_prog.empl_id')
                ->leftJoin('tbl_invb2b', 'tbl_invb2b.schprog_id', '=', 'tbl_sch_prog.id')
                ->select(
                    'tbl_sch.sch_id',
                    'tbl_sch_prog.id',
                    'tbl_sch.sch_name as school_name',
                    // 'tbl_prog.prog_program as program_name',
                    DB::raw('(CASE
                            WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                            ELSE tbl_prog.prog_program
                    END) AS program_name'),
                    'tbl_sch_prog.success_date',
                    'users.id as pic_id',
                    DB::raw('CONCAT(users.first_name," ",COALESCE(users.last_name, "")) as pic_name')
                )->where('tbl_sch_prog.status', 1)->whereNull('tbl_invb2b.schprog_id')
                ->orderBy('tbl_sch_prog.success_date', 'DESC')
        )->filterColumn(
            'pic_name',
            function ($query, $keyword) {
                $sql = 'CONCAT(users.first_name," ",users.last_name) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )
            ->make(true);
    }

    public function getAllInvoiceSchDataTables()
    {
        return datatables::eloquent(
            Invb2b::leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
                ->leftJoin('tbl_sch', 'tbl_sch_prog.sch_id', '=', 'tbl_sch.sch_id')
                ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_sch_prog.prog_id')
                ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                ->select(
                    'tbl_invb2b.invb2b_num',
                    'tbl_sch.sch_name as school_name',
                    // 'tbl_prog.prog_program as program_name',
                    DB::raw('(CASE
                            WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                            ELSE tbl_prog.prog_program
                    END) AS program_name'),
                    'tbl_invb2b.schprog_id',
                    'tbl_invb2b.invb2b_id',
                    'tbl_invb2b.invb2b_status',
                    'tbl_invb2b.invb2b_pm',
                    'tbl_invb2b.created_at',
                    'tbl_invb2b.invb2b_duedate',
                    'tbl_invb2b.currency',
                    'tbl_invb2b.invb2b_totpriceidr',
                    'tbl_invb2b.invb2b_totprice',
                )
                ->where('tbl_sch_prog.status', 1)
                ->orderBy('tbl_invb2b.created_at', 'DESC')

        )->make(true);
    }

    // Partner Program
    public function getAllInvoiceNeededCorpDataTables()
    {
        return datatables::eloquent(
            PartnerProg::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')
                ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_partner_prog.prog_id')
                ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                ->leftJoin('users', 'users.id', '=', 'tbl_partner_prog.empl_id')
                ->leftJoin('tbl_invb2b', 'tbl_invb2b.partnerprog_id', '=', 'tbl_partner_prog.id')
                ->select(
                    'tbl_partner_prog.id',
                    'tbl_corp.corp_id',
                    'tbl_corp.corp_name as partner_name',
                    // 'tbl_prog.prog_program as program_name',
                    DB::raw('(CASE
                            WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                            ELSE tbl_prog.prog_program
                    END) AS program_name'),
                    'tbl_partner_prog.success_date',
                    'users.id as pic_id',
                    DB::raw('CONCAT(users.first_name," ",COALESCE(users.last_name, "")) as pic_name')
                )->where('tbl_partner_prog.status', 1)->whereNull('tbl_invb2b.partnerprog_id')
                ->orderBy('tbl_partner_prog.success_date', 'DESC')
        )->filterColumn(
            'pic_name',
            function (
                $query,
                $keyword
            ) {
                $sql = 'CONCAT(users.first_name," ",users.last_name) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )->make(true);
    }

    public function getAllInvoiceCorpDataTables()
    {
        return datatables::eloquent(
            Invb2b::leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
                ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')
                ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_partner_prog.prog_id')
                ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                ->select(
                    'tbl_invb2b.invb2b_num',
                    'tbl_corp.corp_name',
                    // 'tbl_prog.prog_program as program_name',
                    DB::raw('(CASE
                            WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                            ELSE tbl_prog.prog_program
                    END) AS program_name'),
                    'tbl_invb2b.partnerprog_id',
                    'tbl_invb2b.invb2b_id',
                    'tbl_invb2b.invb2b_status',
                    'tbl_invb2b.invb2b_pm',
                    'tbl_invb2b.created_at',
                    'tbl_invb2b.invb2b_duedate',
                    'tbl_invb2b.currency',
                    'tbl_invb2b.invb2b_totpriceidr',
                    'tbl_invb2b.invb2b_totprice',
                )
                ->where('tbl_partner_prog.status', 1)
                ->orderBy('tbl_invb2b.created_at', 'DESC')
            // ->where('tbl_invb2b.invb2b_status', 1)

        )->make(true);
    }

    // Referral
    public function getAllInvoiceNeededReferralDataTables()
    {
        return datatables::eloquent(
            Referral::leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_referral.partner_id')
                ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_referral.prog_id')
                ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                ->leftJoin('users', 'users.id', '=', 'tbl_referral.empl_id')
                ->leftJoin('tbl_invb2b', 'tbl_invb2b.ref_id', '=', 'tbl_referral.id')
                ->select(
                    'tbl_referral.id',
                    'tbl_corp.corp_id',
                    'tbl_corp.corp_name as partner_name',
                    DB::raw('(CASE tbl_referral.referral_type
                                WHEN "Out" THEN tbl_referral.additional_prog_name
                                WHEN "In" 
                                    THEN 
                                        (CASE
                                        WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                                            ELSE tbl_prog.prog_program
                                        END) 
                            END) AS program_name'),
                    'tbl_referral.number_of_student',
                    'tbl_referral.ref_date',
                    'users.id as pic_id',
                    DB::raw('CONCAT(users.first_name," ",COALESCE(users.last_name, "")) as pic_name')
                )->where('tbl_referral.referral_type', 'Out')->whereNull('tbl_invb2b.ref_id')
                ->orderBy('tbl_referral.ref_date', 'DESC')
        )->filterColumn(
            'pic_name',
            function (
                $query,
                $keyword
            ) {
                $sql = 'CONCAT(users.first_name," ",users.last_name) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )->make(true);
    }

    public function getAllInvoiceReferralDataTables()
    {
        return datatables::eloquent(
            Invb2b::leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')
                ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_referral.partner_id')
                ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_referral.prog_id')
                ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                ->select(
                    'tbl_invb2b.invb2b_num',
                    'tbl_corp.corp_name as partner_name',
                    DB::raw('(CASE
                                WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                                ELSE tbl_prog.prog_program
                            END) AS program_name'),
                    'tbl_invb2b.ref_id',
                    'tbl_invb2b.invb2b_id',
                    'tbl_invb2b.invb2b_status',
                    'tbl_invb2b.invb2b_pm',
                    'tbl_invb2b.created_at',
                    'tbl_invb2b.invb2b_duedate',
                    'tbl_invb2b.currency',
                    'tbl_invb2b.invb2b_totpriceidr',
                    'tbl_invb2b.invb2b_totprice',
                )
                ->where('tbl_referral.referral_type', 'Out')
                ->orderBy('tbl_invb2b.created_at', 'DESC')

        )->make(true);
    }

    public function getInvoiceB2bBySchProg($schprog_id)
    {
        return Invb2b::where('schprog_id', $schprog_id)->first();
    }

    public function getInvoiceB2bByPartnerProg($partnerprog_id)
    {
        return Invb2b::where('partnerprog_id', $partnerprog_id)->first();
    }

    public function getInvoiceB2bByInvId($invb2b_id)
    {
        return Invb2b::where('invb2b_id', $invb2b_id)->get();
    }

    public function getInvoiceB2bById($invb2b_num)
    {
        return Invb2b::find($invb2b_num);
    }

    public function getAllInvoiceSchool()
    {
        return Invb2b::whereNotNull('schprog_id')->get();
    }

    public function deleteInvoiceB2b($invb2b_num)
    {
        return Invb2b::destroy($invb2b_num);
    }

    public function createInvoiceB2b(array $invoices)
    {
        return Invb2b::create($invoices);
    }

    public function insertInvoiceB2b(array $invoices)
    {
        return Invb2b::insert($invoices);
    }

    public function updateInvoiceB2b($invb2b_num, array $newInvoices)
    {
        return Invb2b::find($invb2b_num)->update($newInvoices);
    }

    public function getReportInvoiceB2b($start_date = null, $end_date = null)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        $queryInv = Invb2b::leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')
            ->where(
                DB::raw('(CASE
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.referral_type 
                            END)'),
                DB::raw('(CASE
                                WHEN tbl_invb2b.ref_id > 0 THEN "Out" 
                                ELSE 1
                            END)')
            );

        if (isset($start_date) && isset($end_date)) {
            $queryInv->whereDate('tbl_invb2b.created_at', '>=', $start_date)
                ->whereDate('tbl_invb2b.created_at', '<=', $end_date);
        } else if (isset($start_date) && !isset($end_date)) {
            $queryInv->whereDate('tbl_invb2b.created_at', '>=', $start_date);
        } else if (!isset($start_date) && isset($end_date)) {
            $queryInv->whereDate('tbl_invb2b.created_at', '<=', $end_date);
        } else {
            $queryInv->whereBetween('tbl_invb2b.created_at', [$firstDay, $lastDay]);
        }

        return $queryInv->withCount('inv_detail')->get();
    }


    public function getReportUnpaidInvoiceB2b($start_date = null, $end_date = null)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        $whereBy = DB::raw('(CASE 
                            WHEN tbl_receipt.id is not null THEN
                                tbl_receipt.created_at
                            ELSE 
                                (CASE 
                                    WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                                        tbl_invb2b.invb2b_duedate 
                                    WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                        tbl_invdtl.invdtl_duedate
                                END)
                        END)');

        $invoiceB2b = Invb2b::leftJoin('tbl_invdtl', 'tbl_invdtl.invb2b_id', '=', 'tbl_invb2b.invb2b_id')
            ->leftJoin(
                'tbl_receipt',
                DB::raw('(CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                            tbl_receipt.invb2b_id 
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_receipt.invdtl_id
                        ELSE null
                    END )'),
                DB::raw('CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                            tbl_invb2b.invb2b_id 
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_invdtl.invdtl_id
                        ELSE null
                    END')
            )->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')


            ->select(
                'tbl_invb2b.invb2b_id',
                'tbl_invb2b.schprog_id',
                'tbl_invb2b.partnerprog_id',
                'tbl_invb2b.ref_id',
                'tbl_invb2b.invb2b_duedate',
                DB::raw('(CASE
                            WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                                tbl_invb2b.invb2b_totpriceidr 
                            WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_invdtl.invdtl_amountidr
                            ELSE null
                        END) as total_price_inv'),
                'tbl_receipt.receipt_id',
                'tbl_receipt.receipt_amount_idr',
                'tbl_receipt.created_at as paid_date',
                'tbl_invdtl.invdtl_installment',
                'tbl_invdtl.invdtl_id',
            )
            ->where(
                DB::raw('(CASE
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.referral_type
                            
                            END)'),
                DB::raw('(CASE
                                WHEN tbl_invb2b.ref_id > 0 THEN "Out" 
                                ELSE 1
                            END)')
            );

        if (isset($start_date) && isset($end_date)) {
            return $invoiceB2b->whereBetween($whereBy, [$start_date, $end_date])
                ->orderBy('invb2b_id', 'asc')
                ->orderBy('invdtl_id', 'asc')
                ->get();
        } else if (isset($start_date) && !isset($end_date)) {
            return $invoiceB2b->whereDate($whereBy, '>=', $start_date)
                ->orderBy('invb2b_id', 'asc')
                ->orderBy('invdtl_id', 'asc')
                ->get();
        } else if (!isset($start_date) && isset($end_date)) {
            return $invoiceB2b->whereDate($whereBy, '<=', $end_date)
                ->orderBy('invb2b_id', 'asc')
                ->orderBy('invdtl_id', 'asc')
                ->get();
        } else {
            return $invoiceB2b->whereBetween($whereBy, [$firstDay, $lastDay])
                ->orderBy('invb2b_id', 'asc')
                ->orderBy('invdtl_id', 'asc')
                ->get();
        }
    }

    public function getTotalPartnershipProgram($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        $schProg = Invb2b::leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->select(DB::raw("'sch_prog' as 'type'"), 'tbl_invb2b.invb2b_totpriceidr')
            ->where('tbl_sch_prog.status', '1')
            ->whereYear('tbl_sch_prog.created_at', '=', $year)
            ->whereMonth('tbl_sch_prog.created_at', '=', $month);

        $partnerProg = Invb2b::leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->select(DB::raw("'partner_prog' as 'type'"), 'tbl_invb2b.invb2b_totpriceidr')
            ->where('tbl_partner_prog.status', '1')
            ->whereYear('tbl_partner_prog.created_at', '=', $year)
            ->whereMonth('tbl_partner_prog.created_at', '=', $month)
            ->union($schProg)
            ->get();

        return $partnerProg;
    }

    public function getTotalInvoiceNeeded($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        $schProg  = SchoolProgram::leftJoin('tbl_invb2b', 'tbl_invb2b.schprog_id', '=', 'tbl_sch_prog.id')
            ->select(DB::raw("count('tbl_sch_prog.id') as count_invoice_needed"))
            ->where('tbl_sch_prog.status', 1)
            ->whereYear('tbl_sch_prog.success_date', '=', $year)
            ->whereMonth('tbl_sch_prog.success_date', '=', $month)
            ->whereNull('tbl_invb2b.schprog_id')
            ->get();

        $partnerProg  = PartnerProg::leftJoin('tbl_invb2b', 'tbl_invb2b.partnerprog_id', '=', 'tbl_partner_prog.id')
            ->select(DB::raw("count('tbl_partner_prog.id') as count_invoice_needed"))
            ->where('tbl_partner_prog.status', 1)->whereNull('tbl_invb2b.partnerprog_id')
            ->whereYear('tbl_partner_prog.success_date', '=', $year)
            ->whereMonth('tbl_partner_prog.success_date', '=', $month)
            ->get();

        $referral  = Referral::leftJoin('tbl_invb2b', 'tbl_invb2b.ref_id', '=', 'tbl_referral.id')
            ->select(DB::raw("count('tbl_referral.id') as count_invoice_needed"))
            ->where('tbl_referral.referral_type', 'Out')->whereNull('tbl_invb2b.ref_id')
            ->whereYear('tbl_referral.ref_date', '=', $year)
            ->whereMonth('tbl_referral.ref_date', '=', $month)
            ->get();

        $collection = collect($schProg);
        return $collection->merge($partnerProg)->merge($referral);
    }

    public function getTotalInvoice($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        $whereBy = DB::raw('(CASE
                            WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                                tbl_invb2b.invb2b_duedate 
                            WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_invdtl.invdtl_duedate
                        END)');

        return Invb2b::leftJoin('tbl_invdtl', 'tbl_invdtl.invb2b_id', '=', 'tbl_invb2b.invb2b_id')
            ->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')
            // ->select(DB::raw('COUNT(invb2b_num) as count_invoice'), DB::raw('CAST(sum(invb2b_totpriceidr) as integer) as total'))
            ->select(
                'tbl_invb2b.invb2b_num',
                'tbl_invdtl.invdtl_id',
                'tbl_invb2b.invb2b_totpriceidr',
                'tbl_invb2b.invb2b_pm',
                'tbl_invdtl.invdtl_amountidr'
            )
            ->whereYear($whereBy, '=', $year)
            ->whereMonth($whereBy, '=', $month)
            ->where(
                DB::raw('(CASE
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.referral_type
                            ELSE NULL
                            END)'),
                DB::raw('(CASE
                                WHEN tbl_invb2b.ref_id > 0 THEN "Out"
                            ELSE 1
                            END)')
            )
            ->get();
    }

    public function getTotalRefundRequest($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        $schProg = SchoolProgram::leftJoin('tbl_invb2b', 'tbl_invb2b.schprog_id', '=', 'tbl_sch_prog.id')
            ->select(DB::raw("count('tbl_sch_prog.id') as count_refund_request"))
            ->where('tbl_sch_prog.status', 3)
            ->where('tbl_invb2b.invb2b_status', 1)
            ->whereYear('tbl_sch_prog.refund_date', '=', $year)
            ->whereMonth('tbl_sch_prog.refund_date', '=', $month)
            ->get();

        $partnerProg = PartnerProg::leftJoin('tbl_invb2b', 'tbl_invb2b.partnerprog_id', '=', 'tbl_partner_prog.id')
            ->select(DB::raw("count('tbl_partner_prog.id') as count_refund_request"))
            ->where('tbl_partner_prog.status', 3)
            ->where('tbl_invb2b.invb2b_status', 1)
            ->whereYear('tbl_partner_prog.refund_date', '=', $year)
            ->whereMonth('tbl_partner_prog.refund_date', '=', $month)
            ->get();

        $collection = collect($schProg);
        return $collection->merge($partnerProg);
    }

    public function getInvoiceOutstandingPayment($monthYear, $type, $start_date = null, $end_date = null)
    {
        $whereBy = DB::raw('(CASE
                            WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                                tbl_invb2b.invb2b_duedate 
                            WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_invdtl.invdtl_duedate
                        END)');

        if (isset($monthYear)) {
            $year = date('Y', strtotime($monthYear));
            $month = date('m', strtotime($monthYear));
        }

        $queryInv = Invb2b::leftJoin('tbl_invdtl', 'tbl_invdtl.invb2b_id', '=', 'tbl_invb2b.invb2b_id')
            ->leftJoin(
                'tbl_receipt',
                DB::raw('(CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                            tbl_receipt.invb2b_id 
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_receipt.invdtl_id
                        ELSE null
                    END )'),
                DB::raw('CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                            tbl_invb2b.invb2b_id 
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_invdtl.invdtl_id
                        ELSE null
                    END')
            )
            ->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('tbl_sch', 'tbl_sch_prog.sch_id', '=', 'tbl_sch.sch_id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')
            ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', DB::raw('(CASE WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.corp_id WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.partner_id ELSE NULL END)'))
            ->leftJoin(
                'tbl_prog',
                'tbl_prog.prog_id',
                '=',
                DB::raw('(CASE 
                            WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.prog_id
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.prog_id
                            ELSE NULL
                            END)')
            )
            ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id');

        switch ($type) {
            case 'paid':
                $queryInv->select(
                    'tbl_invb2b.invb2b_id as invoice_id',
                    DB::raw('(CASE 
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch.sch_name
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_corp.corp_name
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_corp.corp_name
                            ELSE NULL
                            END) as full_name'),
                    DB::raw('(CASE
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.additional_prog_name 
                                ELSE
                                    (CASE
                                        WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                                        ELSE tbl_prog.prog_program
                                    END)
                            END) AS program_name'),
                    'tbl_invb2b.invb2b_totpriceidr as total_price_inv',
                    'tbl_invdtl.invdtl_installment as installment_name',
                    DB::raw("'B2B' as type"),
                    DB::raw('tbl_receipt.receipt_amount_idr as total'),
                )->whereNotNull('tbl_receipt.id');

                if (isset($monthYear)) {
                    $queryInv->whereYear('tbl_receipt.created_at', '=', $year)
                        ->whereMonth('tbl_receipt.created_at', '=', $month);
                } else {
                    $queryInv->whereBetween('tbl_receipt.created_at', [$start_date, $end_date]);
                }
                break;

            case 'unpaid':
                $queryInv->select(
                    'tbl_invb2b.invb2b_id as invoice_id',
                    DB::raw('(CASE 
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch.sch_name
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_corp.corp_name
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_corp.corp_name
                            ELSE NULL
                            END) as full_name'),
                    DB::raw('(CASE
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.additional_prog_name 
                                ELSE
                                    (CASE
                                        WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                                        ELSE tbl_prog.prog_program
                                    END)
                            END) AS program_name'),
                    'tbl_invdtl.invdtl_installment as installment_name',
                    DB::raw("'B2B' as type"),
                    DB::raw('(CASE
                            WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                                tbl_invb2b.invb2b_totpriceidr 
                            WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_invdtl.invdtl_amountidr
                            ELSE null
                        END) as total'),
                    // DB::raw("'start_data '" . $start_date . "as start_date"),

                )->whereNull('tbl_receipt.id');

                if (isset($monthYear)) {
                    $queryInv->whereYear($whereBy, '=', $year)
                        ->whereMonth($whereBy, '=', $month);
                } else {
                    $queryInv->whereBetween($whereBy, [$start_date, $end_date]);
                }
                break;
        }

        $queryInv->where(
            DB::raw('(CASE
                        WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                        WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                        WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.referral_type
                    ELSE NULL
                    END)'),
            DB::raw('(CASE
                        WHEN tbl_invb2b.ref_id > 0 THEN "Out"
                    ELSE 1
                    END)')
        );
        // ->groupBy('tbl_invb2b.invb2b_id');


        return $queryInv->get();
    }

    public function getRevenueByYear($year)
    {
        return Invb2b::leftJoin('tbl_invdtl', 'tbl_invdtl.invb2b_id', '=', 'tbl_invb2b.invb2b_id')
            ->leftJoin(
                'tbl_receipt',
                DB::raw('(CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                            tbl_receipt.invb2b_id 
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                            tbl_receipt.invdtl_id
                        ELSE null
                    END )'),
                DB::raw('CASE
                        WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                            tbl_invb2b.invb2b_id 
                        WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                            tbl_invdtl.invdtl_id
                        ELSE null
                    END')
            )
            ->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')
            ->select(DB::raw('SUM(tbl_receipt.receipt_amount_idr) as total'), DB::raw('MONTH(tbl_receipt.created_at) as month'))
            ->where(
                DB::raw('(CASE
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                                WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.referral_type
                            ELSE NULL
                            END)'),
                DB::raw('(CASE
                                WHEN tbl_invb2b.ref_id > 0 THEN "Out"
                            ELSE 1
                            END)')
            )
            ->whereNotNull('tbl_receipt.id')
            ->whereYear('tbl_receipt.created_at', '=', $year)
            ->groupBy(DB::raw('MONTH(tbl_receipt.created_at)'))
            ->get();
    }

    # CRM
    public function getAllInvoiceSchoolFromCRM()
    {
        return V1InvoiceSchool::all();
    }
}

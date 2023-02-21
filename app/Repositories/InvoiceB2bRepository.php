<?php

namespace App\Repositories;

use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Models\Invb2b;
use App\Models\PartnerProg;
use App\Models\SchoolProgram;
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
        )->filterColumn(
            'pic_name',
            function ($query, $keyword) {
                $sql = 'CONCAT(users.first_name," ",users.last_name) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )->make(true);
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
                ->whereIn('tbl_sch_prog.status', [1, 3])
            // ->where('tbl_invb2b.invb2b_status', 1)

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
                ->whereIn('tbl_partner_prog.status', [1, 3])
            // ->where('tbl_invb2b.invb2b_status', 1)

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

    public function deleteInvoiceB2b($invb2b_num)
    {
        return Invb2b::destroy($invb2b_num);
    }

    public function createInvoiceB2b(array $invoices)
    {
        return Invb2b::create($invoices);
    }

    public function updateInvoiceB2b($invb2b_num, array $newInvoices)
    {
        return Invb2b::find($invb2b_num)->update($newInvoices);
    }

    public function getReportInvoiceB2b($start_date = null, $end_date = null, $whereBy)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        $queryInv = Invb2b::leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->where(DB::raw('(CASE
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                            
                            END)'), 1);

        if (isset($start_date) && isset($end_date)) {
            $queryInv->whereDate('tbl_invb2b.' . $whereBy, '>=', $start_date)
                ->whereDate('tbl_invb2b.' . $whereBy, '<=', $end_date);
        } else if (isset($start_date) && !isset($end_date)) {
            $queryInv->whereDate('tbl_invb2b.' . $whereBy, '>=', $start_date);
        } else if (!isset($start_date) && isset($end_date)) {
            $queryInv->whereDate('tbl_invb2b.' . $whereBy, '<=', $end_date);
        } else {
            $queryInv->whereBetween('tbl_invb2b.' . $whereBy, [$firstDay, $lastDay]);
        }

        return $queryInv->get();
    }


    public function getReportUnpaidInvoiceB2b($start_date = null, $end_date = null)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

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


            ->select(
                'tbl_invb2b.invb2b_id',
                'tbl_invb2b.schprog_id',
                'tbl_invb2b.partnerprog_id',
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
            ->where(DB::raw('(CASE
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                            
                            END)'), 1);

        if (isset($start_date) && isset($end_date)) {
            return $invoiceB2b->whereBetween('invb2b_duedate', [$start_date, $end_date])
                ->orderBy('invb2b_id', 'asc')
                ->orderBy('invdtl_id', 'asc')
                ->get();
        } else if (isset($start_date) && !isset($end_date)) {
            return $invoiceB2b->whereDate('invb2b_duedate', '>=', $start_date)
                ->orderBy('invb2b_id', 'asc')
                ->orderBy('invdtl_id', 'asc')
                ->get();
        } else if (!isset($start_date) && isset($end_date)) {
            return $invoiceB2b->whereDate('invb2b_duedate', '<=', $end_date)
                ->orderBy('invb2b_id', 'asc')
                ->orderBy('invdtl_id', 'asc')
                ->get();
        } else {
            return $invoiceB2b->whereBetween('invb2b_duedate', [$firstDay, $lastDay])
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

        $collection = collect($schProg);
        return $collection->merge($partnerProg);
    }

    public function getTotalInvoice($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        return Invb2b::leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->select(DB::raw('COUNT(invb2b_num) as count_invoice'), DB::raw('CAST(sum(invb2b_totpriceidr) as integer) as total'))
            ->whereYear('tbl_invb2b.invb2b_duedate', '=', $year)
            ->whereMonth('tbl_invb2b.invb2b_duedate', '=', $month)
            ->where(DB::raw('(CASE
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                            ELSE NULL
                            END)'), 1)
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
            ->whereYear('tbl_invb2b.invb2b_duedate', '=', $year)
            ->whereMonth('tbl_invb2b.invb2b_duedate', '=', $month)
            ->get();

        $partnerProg = PartnerProg::leftJoin('tbl_invb2b', 'tbl_invb2b.partnerprog_id', '=', 'tbl_partner_prog.id')
            ->select(DB::raw("count('tbl_partner_prog.id') as count_refund_request"))
            ->where('tbl_partner_prog.status', 3)
            ->where('tbl_invb2b.invb2b_status', 1)
            ->whereYear('tbl_invb2b.invb2b_duedate', '=', $year)
            ->whereMonth('tbl_invb2b.invb2b_duedate', '=', $month)
            ->get();

        $collection = collect($schProg);
        return $collection->merge($partnerProg);
    }

    public function getInvoiceOutstandingPayment($monthYear, $type, $start_date = null, $end_date = null)
    {
        // TODO: Outstanding payment by period

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
            ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')
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
                    DB::raw('(CASE 
                            WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch.sch_name
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_corp.corp_name
                            ELSE NULL
                            END) as full_name'),
                    DB::raw('(CASE
                            WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                            ELSE tbl_prog.prog_program
                            END) AS program_name'),
                    'tbl_invb2b.invb2b_totpriceidr as total_price_inv',
                    DB::raw('tbl_receipt.receipt_amount_idr as total')
                )->has('receipt');
                break;

            case 'unpaid':
                $queryInv->select(
                    DB::raw('(CASE 
                            WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch.sch_name
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_corp.corp_name
                            ELSE NULL
                            END) as full_name'),
                    DB::raw('(CASE
                            WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                            ELSE tbl_prog.prog_program
                            END) AS program_name'),
                    DB::raw('(CASE
                            WHEN tbl_invb2b.invb2b_pm = "Full Payment" THEN 
                                tbl_invb2b.invb2b_totpriceidr 
                            WHEN tbl_invb2b.invb2b_pm = "Installment" THEN 
                                tbl_invdtl.invdtl_amountidr
                            ELSE null
                        END) as total'),
                    // DB::raw("'start_data '" . $start_date . "as start_date"),

                )->doesnthave('receipt');
                break;
        }

        if (isset($monthYear)) {
            $queryInv->whereYear('tbl_invb2b.invb2b_duedate', '=', $year)
                ->whereMonth('tbl_invb2b.invb2b_duedate', '=', $month);
        } else {
            $queryInv->whereBetween('tbl_invb2b.invb2b_duedate', [$start_date, $end_date]);
        }

        $queryInv->where(DB::raw('(CASE
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                            ELSE NULL
                            END)'), 1);
        // ->groupBy('tbl_invb2b.invb2b_id');


        return $queryInv->get();
    }

    // TODO: Revenue By Year
    public function getRevenueByYear($year)
    {
        return Invb2b::leftJoin('tbl_receipt', 'tbl_receipt.invb2b_id', '=', 'tbl_invb2b.invb2b_id')
            ->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->select(DB::raw('SUM(tbl_receipt.receipt_amount_idr) as total'), DB::raw('MONTH(tbl_invb2b.invb2b_duedate) as month'))
            ->whereYear('tbl_invb2b.invb2b_duedate', '=', $year)
            ->where(DB::raw('(CASE
                                WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                                WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                            ELSE NULL
                            END)'), 1)
            ->whereHas('receipt')
            ->groupBy(DB::raw('MONTH(tbl_invb2b.invb2b_duedate)'))
            ->get();
    }
}

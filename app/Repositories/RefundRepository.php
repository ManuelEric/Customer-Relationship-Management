<?php

namespace App\Repositories;

use App\Interfaces\RefundRepositoryInterface;
use App\Models\Invb2b;
use App\Models\Receipt;
use App\Models\Refund;
use DataTables;
use Illuminate\Support\Facades\DB;

class RefundRepository implements RefundRepositoryInterface
{

    public function getAllRefundDataTables($status)
    {
        $refunded_invoice = Refund::select('inv_id')->get()->toArray();
        $refunded_invoiceB2b = Refund::pluck('invb2b_id')->toArray();
        switch ($status) {
            case "needed":
            default:
                $query = Receipt::leftJoin('tbl_inv', 'tbl_inv.inv_id', '=', 'tbl_receipt.inv_id')->leftJoin('tbl_invb2b', 'tbl_invb2b.invb2b_id', '=', 'tbl_receipt.invb2b_id')->leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')->leftJoin('tbl_reason as b2c_c_r', 'b2c_c_r.reason_id', '=', 'tbl_client_prog.reason_id')->leftJoin('tbl_reason as b2b_s_r', 'b2b_s_r.reason_id', '=', 'tbl_sch_prog.reason_id')->leftJoin('tbl_reason as b2b_p_r', 'b2b_p_r.reason_id', '=', 'tbl_partner_prog.reason_id')->leftJoin('users as b2c_c_u', 'b2c_c_u.id', '=', 'tbl_client_prog.empl_id')->leftJoin('users as b2b_s_u', 'b2b_s_u.id', '=', 'tbl_sch_prog.empl_id')->leftJoin('users as b2b_p_u', 'b2b_p_u.id', '=', 'tbl_partner_prog.empl_id')->leftJoin('program as b2c_c_p', 'b2c_c_p.prog_id', '=', 'tbl_client_prog.prog_id')->leftJoin('program as b2b_s_p', 'b2b_s_p.prog_id', '=', 'tbl_sch_prog.prog_id')->leftJoin('program as b2b_p_p', 'b2b_p_p.prog_id', '=', 'tbl_partner_prog.prog_id')->leftJoin('tbl_main_prog as b2c_c_mp', 'b2c_c_mp.id', '=', 'b2c_c_p.main_prog_id')->leftJoin('tbl_main_prog as b2b_s_mp', 'b2b_s_mp.id', '=', 'b2b_s_p.main_prog_id')->leftJoin('tbl_main_prog as b2b_p_mp', 'b2b_p_mp.id', '=', 'b2b_p_p.main_prog_id')->leftJoin('tbl_sub_prog as b2c_c_sp', 'b2c_c_sp.id', '=', 'b2c_c_p.sub_prog_id')->leftJoin('tbl_sub_prog as b2b_s_sp', 'b2b_s_sp.id', '=', 'b2b_s_p.sub_prog_id')->leftJoin('tbl_sub_prog as b2b_p_sp', 'b2b_p_sp.id', '=', 'b2b_p_p.sub_prog_id')->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')->leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_sch_prog.sch_id')->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')->leftJoin('tbl_refund as b2c_refund', 'b2c_refund.inv_id', '=', 'tbl_inv.inv_id')->leftJoin('tbl_refund as b2b_refund', 'b2b_refund.invb2b_id', '=', 'tbl_invb2b.invb2b_id')->
                    // whereRaw('tbl_receipt.inv_id NOT IN (SELECT inv_id FROM tbl_refund)')->
                    // whereRaw('(CASE 
                    //     WHEN tbl_receipt.receipt_cat = "student" THEN tbl_receipt.inv_id NOT IN (SELECT inv_id FROM tbl_refund)
                    //     WHEN tbl_receipt.receipt_cat = "school" OR tbl_receipt.receipt_cat = "partner" THEN tbl_receipt.invb2b_id NOT IN (SELECT invb2b_id FROM tbl_refund)
                    // END)')->
                    // where(DB::raw('(CASE 
                    //             WHEN tbl_receipt.receipt_cat = "student" THEN tbl_receipt.inv_id NOT IN (SELECT inv_id FROM tbl_refund)
                    //             WHEN tbl_receipt.receipt_cat = "school" OR tbl_receipt.receipt_cat = "partner" THEN tbl_receipt.invb2b_id NOT IN (SELECT invb2b_id FROM tbl_refund)
                    //         END)'))->       
                    whereNull('b2c_refund.inv_id')->whereNull('b2b_refund.invb2b_id')->where(function ($q) {
                        $q->where('tbl_client_prog.status', 3)->orWhere('tbl_sch_prog.status', 3)->orWhere('tbl_partner_prog.status', 3);
                    })->select([
                        DB::raw('(CASE 
                                    WHEN tbl_receipt.receipt_cat = "student" THEN tbl_client_prog.refund_date
                                    WHEN tbl_receipt.receipt_cat = "school" THEN tbl_sch_prog.refund_date
                                    WHEN tbl_receipt.receipt_cat = "partner" THEN tbl_partner_prog.refund_date
                                END) as refund_date'),
                        DB::raw('(CASE
                                    WHEN tbl_receipt.receipt_cat = "student" THEN tbl_inv.inv_totalprice_idr
                                    WHEN tbl_receipt.receipt_cat = "school" OR tbl_receipt.receipt_cat = "partner" THEN tbl_invb2b.invb2b_totpriceidr
                                END) as total_price'),
                        DB::raw('(CASE 
                                    WHEN tbl_receipt.receipt_cat = "student" THEN (SELECT SUM(re.receipt_amount_idr) FROM tbl_receipt re WHERE re.inv_id = tbl_inv.inv_id)
                                    WHEN tbl_receipt.receipt_cat = "school" OR tbl_receipt.receipt_cat = "partner" THEN (SELECT SUM(re.receipt_amount_idr) FROM tbl_receipt re WHERE re.invb2b_id = tbl_invb2b.invb2b_id)
                                END) as total_paid'),
                        DB::raw('(CASE 
                                    WHEN tbl_receipt.receipt_cat = "student" THEN tbl_client_prog.refund_notes
                                    WHEN tbl_receipt.receipt_cat = "school" THEN tbl_sch_prog.refund_notes
                                    WHEN tbl_receipt.receipt_cat = "partner" THEN tbl_partner_prog.refund_notes
                                END) as refund_notes'),
                        DB::raw('(CASE 
                                    WHEN tbl_receipt.receipt_cat = "student" THEN b2c_c_r.reason_name
                                    WHEN tbl_receipt.receipt_cat = "school" THEN b2b_s_r.reason_name
                                    WHEN tbl_receipt.receipt_cat = "partner" THEN b2b_p_r.reason_name
                                END) as refund_reason'),
                        DB::raw('(CASE 
                                    WHEN tbl_receipt.receipt_cat = "student" THEN CONCAT(b2c_c_u.first_name, " ", COALESCE(b2c_c_u.last_name, ""))
                                    WHEN tbl_receipt.receipt_cat = "school" THEN CONCAT(b2b_s_u.first_name, " ", COALESCE(b2b_s_u.last_name, ""))
                                    WHEN tbl_receipt.receipt_cat = "partner" THEN CONCAT(b2b_p_u.first_name, " ", COALESCE(b2b_p_u.last_name, ""))
                                END) as pic_name'),
                        DB::raw('(CASE 
                                    WHEN tbl_receipt.receipt_cat = "student" THEN CONCAT(tbl_client.first_name, " ", COALESCE(tbl_client.last_name, ""))
                                    WHEN tbl_receipt.receipt_cat = "school" THEN tbl_sch.sch_name
                                    WHEN tbl_receipt.receipt_cat = "partner" THEN tbl_corp.corp_name
                                END) as client_fullname'),
                        DB::raw('(CASE 
                                    WHEN b2c_c_p.prog_id IS NOT NULL THEN b2c_c_p.program_name
                                    WHEN b2b_s_p.prog_id IS NOT NULL THEN b2b_s_p.program_name
                                    WHEN b2b_p_p.prog_id IS NOT NULL THEN b2b_p_p.program_name
                                END) as program_name'),
                        DB::raw('(CASE 
                                    WHEN tbl_receipt.receipt_cat = "student" THEN tbl_client_prog.clientprog_id
                                    WHEN tbl_receipt.receipt_cat = "school" THEN tbl_sch_prog.id
                                    WHEN tbl_receipt.receipt_cat = "partner" THEN tbl_partner_prog.id
                                END) as b2prog_id'),
                        DB::raw('(CASE 
                                    WHEN tbl_receipt.receipt_cat = "student" THEN tbl_inv.id
                                    WHEN tbl_receipt.receipt_cat = "school" OR tbl_receipt.receipt_cat = "partner" THEN tbl_invb2b.invb2b_num
                                END) as invoiceNum'),

                        'tbl_receipt.id',
                        'tbl_receipt.inv_id',
                        'tbl_receipt.invdtl_id',
                        'tbl_receipt.invb2b_id',
                        'tbl_receipt.receipt_cat'
                    ])->groupBy('tbl_inv.inv_id', 'tbl_invb2b.invb2b_id')->orderBy('tbl_receipt.updated_at', 'desc');

                break;

            case "list":
                $query = Refund::leftJoin('tbl_inv', 'tbl_inv.inv_id', '=', 'tbl_refund.inv_id')->leftJoin('tbl_invb2b', 'tbl_invb2b.invb2b_id', '=', 'tbl_refund.invb2b_id')->leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')->leftJoin('tbl_reason as b2c_c_r', 'b2c_c_r.reason_id', '=', 'tbl_client_prog.reason_id')->leftJoin('tbl_reason as b2b_s_r', 'b2b_s_r.reason_id', '=', 'tbl_sch_prog.reason_id')->leftJoin('tbl_reason as b2b_p_r', 'b2b_p_r.reason_id', '=', 'tbl_partner_prog.reason_id')->leftJoin('users as b2c_c_u', 'b2c_c_u.id', '=', 'tbl_client_prog.empl_id')->leftJoin('users as b2b_s_u', 'b2b_s_u.id', '=', 'tbl_sch_prog.empl_id')->leftJoin('users as b2b_p_u', 'b2b_p_u.id', '=', 'tbl_partner_prog.empl_id')->leftJoin('program as b2c_c_p', 'b2c_c_p.prog_id', '=', 'tbl_client_prog.prog_id')->leftJoin('program as b2b_s_p', 'b2b_s_p.prog_id', '=', 'tbl_sch_prog.prog_id')->leftJoin('program as b2b_p_p', 'b2b_p_p.prog_id', '=', 'tbl_partner_prog.prog_id')->leftJoin('tbl_main_prog as b2c_c_mp', 'b2c_c_mp.id', '=', 'b2c_c_p.main_prog_id')->leftJoin('tbl_main_prog as b2b_s_mp', 'b2b_s_mp.id', '=', 'b2b_s_p.main_prog_id')->leftJoin('tbl_main_prog as b2b_p_mp', 'b2b_p_mp.id', '=', 'b2b_p_p.main_prog_id')->leftJoin('tbl_sub_prog as b2c_c_sp', 'b2c_c_sp.id', '=', 'b2c_c_p.sub_prog_id')->leftJoin('tbl_sub_prog as b2b_s_sp', 'b2b_s_sp.id', '=', 'b2b_s_p.sub_prog_id')->leftJoin('tbl_sub_prog as b2b_p_sp', 'b2b_p_sp.id', '=', 'b2b_p_p.sub_prog_id')->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')->leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_sch_prog.sch_id')->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')->leftJoin('tbl_receipt as b2c_receipt', 'b2c_receipt.inv_id', '=', 'tbl_inv.inv_id')->leftJoin('tbl_receipt as b2b_receipt', 'b2b_receipt.invb2b_id', '=', 'tbl_invb2b.invb2b_id')->select([
                    DB::raw('(CASE
                                    WHEN tbl_refund.inv_id IS NOT NULL THEN CONCAT(tbl_client.first_name COLLATE utf8mb4_unicode_ci, " ", COALESCE(tbl_client.last_name COLLATE utf8mb4_unicode_ci, ""))
                                    WHEN tbl_refund.invb2b_id IS NOT NULL THEN 
                                        CASE
                                            WHEN tbl_invb2b.schprog_id IS NOT NULL THEN tbl_sch.sch_name
                                            WHEN tbl_invb2b.partnerprog_id IS NOT NULL THEN tbl_corp.corp_name
                                        END
                                END) as client_fullname'),
                    DB::raw('(CASE 
                                    WHEN b2c_c_p.prog_id IS NOT NULL THEN b2c_c_p.program_name
                                    WHEN b2b_s_p.prog_id IS NOT NULL THEN b2b_s_p.program_name
                                    WHEN b2b_p_p.prog_id IS NOT NULL THEN b2b_p_p.program_name
                                END) as program_name'),
                    DB::raw('(CASE 
                                    WHEN tbl_refund.inv_id IS NOT NULL THEN tbl_inv.inv_id
                                    WHEN tbl_refund.invb2b_id IS NOT NULL THEN tbl_invb2b.invb2b_id
                                END) as invoiceId'),
                    DB::raw('(CASE
                                    WHEN tbl_refund.inv_id IS NOT NULL THEN b2c_receipt.receipt_cat
                                    WHEN tbl_refund.invb2b_id IS NOT NULL THEN b2b_receipt.receipt_cat
                                END) as receipt_cat'),
                    DB::raw('(CASE 
                                    WHEN tbl_refund.inv_id IS NOT NULL THEN DATEDIFF(tbl_inv.inv_duedate, now())
                                    WHEN tbl_refund.invb2b_id IS NOT NULL THEN DATEDIFF(tbl_invb2b.invb2b_duedate, now())
                                END) as date_difference'),
                    DB::raw('(CASE
                                    WHEN tbl_refund.inv_id IS NOT NULL THEN tbl_inv.inv_totalprice_idr
                                    WHEN tbl_refund.invb2b_id IS NOT NULL THEN tbl_invb2b.invb2b_totpriceidr
                                END) as total_price'),
                    'tbl_refund.id as refund_id',
                    'tbl_refund.refund_amount',
                    'tbl_refund.tax_amount',
                    'tbl_refund.total_refunded',
                    DB::raw('(CASE 
                                    WHEN tbl_refund.inv_id IS NOT NULL THEN b2c_receipt.id
                                    WHEN tbl_refund.invb2b_id IS NOT NULL THEN b2b_receipt.id
                                END) as id')
                ]);
                break;
        }

        return DataTables::eloquent($query)
            ->filterColumn('client_fullname', function ($query, $keyword) {
                $sql = 'CONCAT(tbl_client.first_name, " ", COALESCE(tbl_client.last_name, "")) like ? 
                        OR tbl_sch.sch_name like ? 
                        OR tbl_corp.corp_name like ?';
                $query->whereRaw($sql, ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%"]);
            })
            ->filterColumn('program_name', function ($query, $keyword) {
                $sql = 'CONCAT(b2c_c_p.prog_program COLLATE utf8mb4_unicode_ci, " - ", COALESCE(b2c_c_mp.prog_name COLLATE utf8mb4_unicode_ci, "")) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('refund_date', function ($query, $keyword) {
                $sql = 'tbl_client_prog.refund_date like ? 
                        OR tbl_sch_prog.refund_date like ? 
                        OR tbl_partner_prog.refund_date like ?';
                $query->whereRaw($sql, ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%"]);
            })
            ->filterColumn('pic_name', function ($query, $keyword) {
                $sql = 'CONCAT(b2c_c_u.first_name, " ", COALESCE(b2c_c_u.last_name, "")) like ? 
                        OR CONCAT(b2b_s_u.first_name, " ", COALESCE(b2b_s_u.last_name, "")) like ? 
                        OR CONCAT(b2b_p_u.first_name, " ", COALESCE(b2b_p_u.last_name, "")) like ?';
                $query->whereRaw($sql, ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%"]);
            })
            ->filterColumn('invoiceId', function ($query, $keyword) {
                $sql = 'tbl_inv.inv_id like ? 
                        OR tbl_invb2b.invb2b_id like ?';
                $query->whereRaw($sql, ["%{$keyword}%", "%{$keyword}%"]);
            })
            ->rawColumns(['refund_notes'])
            ->make(true);
    }

    public function getTotalRefundRequest($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        return Receipt::
            leftJoin('tbl_inv', 'tbl_inv.inv_id', '=', 'tbl_receipt.inv_id')->
            leftJoin('tbl_invb2b', 'tbl_invb2b.invb2b_id', '=', 'tbl_receipt.invb2b_id')->
            leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')->
            leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')->
            leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')->
            leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_sch_prog.sch_id')->
            leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')
            ->leftJoin('program as b2c_c_p', 'b2c_c_p.prog_id', '=', 'tbl_client_prog.prog_id')->
            leftJoin('program as b2b_s_p', 'b2b_s_p.prog_id', '=', 'tbl_sch_prog.prog_id')->
            leftJoin('program as b2b_p_p', 'b2b_p_p.prog_id', '=', 'tbl_partner_prog.prog_id')
            ->leftJoin('users as b2c_c_u', 'b2c_c_u.id', '=', 'tbl_client_prog.empl_id')->
            leftJoin('users as b2b_s_u', 'b2b_s_u.id', '=', 'tbl_sch_prog.empl_id')->
            leftJoin('users as b2b_p_u', 'b2b_p_u.id', '=', 'tbl_partner_prog.empl_id')
            ->where(DB::raw('(CASE 
                        WHEN tbl_receipt.receipt_cat = "student" THEN tbl_receipt.inv_id NOT IN (SELECT inv_id FROM tbl_refund)
                        WHEN tbl_receipt.receipt_cat = "school" OR tbl_receipt.receipt_cat = "partner" THEN tbl_receipt.invb2b_id NOT IN (SELECT invb2b_id FROM tbl_refund)
                    END)'))->where(function ($q) {
                $q->where('tbl_client_prog.status', 3)->orWhere('tbl_sch_prog.status', 3)->orWhere('tbl_partner_prog.status', 3);
            })->select(
                DB::raw('(CASE 
                            WHEN tbl_receipt.receipt_cat = "student" THEN CONCAT(b2c_c_u.first_name, " ", COALESCE(b2c_c_u.last_name, ""))
                            WHEN tbl_receipt.receipt_cat = "school" THEN CONCAT(b2b_s_u.first_name, " ", COALESCE(b2b_s_u.last_name, ""))
                            WHEN tbl_receipt.receipt_cat = "partner" THEN CONCAT(b2b_p_u.first_name, " ", COALESCE(b2b_p_u.last_name, ""))
                        END) as pic_name'),
                DB::raw('(CASE 
                            WHEN tbl_receipt.receipt_cat = "student" THEN CONCAT(tbl_client.first_name, " ", COALESCE(tbl_client.last_name, ""))
                            WHEN tbl_receipt.receipt_cat = "school" THEN tbl_sch.sch_name
                            WHEN tbl_receipt.receipt_cat = "partner" THEN tbl_corp.corp_name
                        END) as client_fullname'),
                DB::raw('(CASE 
                            WHEN b2c_c_p.prog_id IS NOT NULL THEN b2c_c_p.program_name
                            WHEN b2b_s_p.prog_id IS NOT NULL THEN b2b_s_p.program_name
                            WHEN b2b_p_p.prog_id IS NOT NULL THEN b2b_p_p.program_name
                        END) as program_name'),
                DB::raw('(CASE 
                            WHEN tbl_receipt.receipt_cat = "student" THEN tbl_client_prog.refund_date
                            WHEN tbl_receipt.receipt_cat = "school" THEN tbl_sch_prog.refund_date
                            WHEN tbl_receipt.receipt_cat = "partner" THEN tbl_partner_prog.refund_date
                        END) as refund_date'),
                DB::raw('(CASE 
                            WHEN tbl_receipt.receipt_cat = "student" THEN tbl_client_prog.clientprog_id
                            WHEN tbl_receipt.receipt_cat = "school" THEN tbl_sch_prog.id
                            WHEN tbl_receipt.receipt_cat = "partner" THEN tbl_partner_prog.id
                        END) as client_prog_id'),
                DB::raw('(CASE 
                            WHEN tbl_receipt.receipt_cat = "student" THEN "client_prog"
                            WHEN tbl_receipt.receipt_cat = "school" THEN "sch_prog"
                            WHEN tbl_receipt.receipt_cat = "partner" THEN "partner_prog"
                        END) as typeprog'),
                DB::raw('(CASE 
                            WHEN tbl_receipt.receipt_cat = "student" THEN tbl_inv.inv_id
                            WHEN tbl_receipt.receipt_cat = "school" THEN tbl_invb2b.invb2b_id
                            WHEN tbl_receipt.receipt_cat = "partner" THEN tbl_invb2b.invb2b_id
                        END) as invoice_id'),
                'tbl_receipt.receipt_id'
            )
            ->groupBy('tbl_inv.inv_id', 'tbl_invb2b.invb2b_id')->get();
    }

    public function getRefundById($refundId)
    {
        return Refund::find($refundId);
    }

    public function getRefundByInvId($invoiceId)
    {
        return Refund::where('inv_id', $invoiceId)->first();
    }

    public function createRefund(array $refundDetails)
    {
        return Refund::create($refundDetails);
    }

    public function updateRefund($refundId, array $newDetails)
    {
        return Refund::whereId($refundId)->update($newDetails);
    }

    public function deleteRefundByRefundId($refundId)
    {
        return Refund::whereId($refundId)->delete();
    }

    public function deleteRefund($invoiceId)
    {
        return Refund::where('inv_id', $invoiceId)->delete();
    }
}

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
        switch ($status) {
            case "needed":
            default:
                # query for client program
                $query = Receipt::leftJoin('tbl_inv', 'tbl_inv.inv_id', '=', 'tbl_receipt.inv_id')
                    ->leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')
                    ->leftJoin('tbl_reason', 'tbl_reason.reason_id', '=', 'tbl_client_prog.reason_id')
                    ->leftJoin('users', 'users.id', '=', 'tbl_client_prog.empl_id')
                    ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
                    ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
                    ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                    ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')
                    ->whereNotIn('tbl_inv.inv_id', Refund::pluck('inv_id'))
                    ->where('tbl_client_prog.status', 3)
                    ->select([
                        'tbl_client_prog.clientprog_id',
                        'tbl_client_prog.refund_date',
                        'tbl_inv.inv_totalprice_idr as total_price',
                        DB::raw('(SELECT SUM(re.receipt_amount_idr) FROM tbl_receipt re WHERE re.inv_id = tbl_inv.inv_id) as total_paid'),
                        'tbl_client_prog.refund_notes',
                        'tbl_reason.reason_name as refund_reason',
                        DB::raw('CONCAT(users.first_name, " ", COALESCE(users.last_name, "")) as pic_name'),
                        'tbl_inv.clientprog_id',
                        'tbl_receipt.id',
                        DB::raw('CONCAT(tbl_client.first_name, " ", COALESCE(tbl_client.last_name, "")) as client_fullname'),
                        DB::raw('CONCAT(prog_program, " - ", COALESCE(tbl_main_prog.prog_name, ""), " / ", COALESCE(tbl_sub_prog.sub_prog_name, "")) as program_name'),                        
                        
                    ]);

                $query = Receipt::
                            leftJoin('tbl_inv', 'tbl_inv.inv_id', '=', 'tbl_receipt.inv_id')->
                            leftJoin('tbl_invb2b', 'tbl_invb2b.invb2b_id', '=', 'tbl_receipt.invb2b_id')->
                            leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')->
                            leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')->
                            leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')->
                            leftJoin('tbl_reason as b2c_c_r', 'b2c_c_r.reason_id', '=', 'tbl_client_prog.reason_id')->
                            leftJoin('tbl_reason as b2b_s_r', 'b2b_s_r.reason_id', '=', 'tbl_sch_prog.reason_id')->
                            leftJoin('tbl_reason as b2b_p_r', 'b2b_p_r.reason_id', '=', 'tbl_partner_prog.reason_id')->
                            leftJoin('users as b2c_c_u', 'b2c_c_u.id', '=', 'tbl_client_prog.empl_id')->
                            leftJoin('users as b2b_s_u', 'b2b_s_u.id', '=', 'tbl_sch_prog.empl_id')->
                            leftJoin('users as b2b_p_u', 'b2b_p_u.id', '=', 'tbl_partner_prog.empl_id')->
                            leftJoin('tbl_prog as b2c_c_p', 'b2c_c_p.prog_id', '=', 'tbl_client_prog.prog_id')->
                            leftJoin('tbl_prog as b2b_s_p', 'b2b_s_p.prog_id', '=', 'tbl_sch_prog.prog_id')->
                            leftJoin('tbl_prog as b2b_p_p', 'b2b_p_p.prog_id', '=', 'tbl_partner_prog.prog_id')->
                            leftJoin('tbl_main_prog as b2c_c_mp', 'b2c_c_mp.id', '=', 'b2c_c_p.main_prog_id')->
                            leftJoin('tbl_main_prog as b2b_s_mp', 'b2b_s_mp.id', '=', 'b2b_s_p.main_prog_id')->
                            leftJoin('tbl_main_prog as b2b_p_mp', 'b2b_p_mp.id', '=', 'b2b_p_p.main_prog_id')->
                            leftJoin('tbl_sub_prog as b2c_c_sp', 'b2c_c_sp.id', '=', 'b2c_c_p.sub_prog_id')->
                            leftJoin('tbl_sub_prog as b2b_s_sp', 'b2b_s_sp.id', '=', 'b2b_s_p.sub_prog_id')->
                            leftJoin('tbl_sub_prog as b2b_p_sp', 'b2b_p_sp.id', '=', 'b2b_p_p.sub_prog_id')->
                            leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')->
                            leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_sch_prog.sch_id')->
                            leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')->
                            where(function($q) {
                                $q->whereNotIn('tbl_inv.inv_id', Refund::pluck('inv_id'))->
                                orWhereNotIn('tbl_invb2b.invb2b_id', Refund::pluck('invb2b_id'));
                            })->
                            where(function($q) {
                                $q->where('tbl_client_prog.status', 3)->
                                orWhere('tbl_sch_prog.status', 3)->
                                orWhere('tbl_partner_prog.status', 3);
                            })->
                            select([
                                # untuk hari senin
                                # pakai case buat select data2nya
                                # misalkan invoice id maka get data yg only buat client
                                # lalu misalkan invoice b2b dan school maka get data yg only buat school
                                # dst

                                'tbl_client_prog.clientprog_id',
                                'tbl_client_prog.refund_date as refund_date',
                                'tbl_inv.inv_totalprice_idr as total_price',
                                DB::raw('(SELECT SUM(re.receipt_amount_idr) FROM tbl_receipt re WHERE re.inv_id = tbl_inv.inv_id) as total_paid'),
                                'tbl_client_prog.refund_notes',
                                'b2c_c_r.reason_name as refund_reason',
                                DB::raw('CONCAT(b2c_c_u.first_name, " ", COALESCE(b2c_c_u.last_name, "")) as pic_name'),
                                'tbl_inv.clientprog_id',
                                'tbl_receipt.id',
                                DB::raw('CONCAT(tbl_client.first_name, " ", COALESCE(tbl_client.last_name, "")) as client_fullname'),
                                DB::raw('CONCAT(b2c_c_p.prog_program, " - ", COALESCE(b2c_c_mp.prog_name, ""), " / ", COALESCE(b2c_c_sp.sub_prog_name, "")) as program_name'),                        
                                #
                                'tbl_sch_prog.id as schprog_id',
                                'tbl_sch_prog.refund_date as refund_date',
                                'tbl_invb2b.invb2b_totpriceidr as total_price',
                                DB::raw('(SELECT SUM(re.receipt_amount_idr) FROM tbl_receipt re WHERE re.invb2b_id = tbl_invb2b.invb2b_id) as total_paid'),
                                'tbl_sch_prog.refund_notes',
                                'b2b_s_r.reason_name as refund_reason',
                                DB::raw('CONCAT(b2b_s_u.first_name, " ", COALESCE(b2b_s_u.last_name, "")) as pic_name'),
                                'tbl_invb2b.schprog_id',
                                DB::raw('tbl_sch.sch_name as client_fullname'),
                                DB::raw('CONCAT(b2b_s_p.prog_program, " - ", COALESCE(b2b_s_mp.prog_name, ""), " / ", COALESCE(b2b_s_sp.sub_prog_name, "")) as program_name'),
                                #
                                'tbl_partner_prog.id as partnerprog_id',
                                'tbl_partner_prog.refund_date',
                                'tbl_invb2b.invb2b_totpriceidr as total_price',
                                DB::raw('(SELECT SUM(re.receipt_amount_idr) FROM tbl_receipt re WHERE re.invb2b_id = tbl_invb2b.invb2b_id) as total_paid'),
                                'tbl_partner_prog.refund_notes',
                                'b2b_p_r.reason_name as refund_reason',
                                DB::raw('CONCAT(b2b_p_u.first_name, " ", COALESCE(b2b_p_u.last_name, "")) as pic_name'),
                                'tbl_invb2b.partnerprog_id',
                                DB::raw('tbl_corp.corp_name as client_fullname'),
                                DB::raw('CONCAT(b2b_p_p.prog_program, " - ", COALESCE(b2b_p_mp.prog_name, ""), " / ", COALESCE(b2b_p_sp.sub_prog_name, "")) as program_name'),
                            ]);
                            
                            
                
                break;

            case "list":
                $query = Refund::leftJoin('tbl_inv', 'tbl_inv.inv_id', '=', 'tbl_refund.inv_id')
                ->leftJoin('tbl_receipt', 'tbl_receipt.inv_id', '=', 'tbl_inv.inv_id')
                ->leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')
                ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
                ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
                ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')
                ->select([
                    'tbl_inv.clientprog_id',
                    'tbl_receipt.id',
                    DB::raw('CONCAT(first_name COLLATE utf8mb4_unicode_ci, " ", COALESCE(last_name COLLATE utf8mb4_unicode_ci, "")) as client_fullname'),
                    DB::raw('CONCAT(prog_program COLLATE utf8mb4_unicode_ci, " - ", COALESCE(tbl_main_prog.prog_name COLLATE utf8mb4_unicode_ci, ""), " / ", COALESCE(tbl_sub_prog.sub_prog_name COLLATE utf8mb4_unicode_ci, "")) as program_name'),
                    'tbl_inv.inv_id',
                    DB::raw('DATEDIFF(inv_duedate, now()) as date_difference'),
                    'tbl_inv.inv_id',
                    'tbl_inv.inv_totalprice_idr as total_price',
                    'tbl_refund.refund_amount',
                    'tbl_refund.tax_amount',
                    'tbl_refund.total_refunded'
                ]);
                break;
        }

        return DataTables::eloquent($query)
            ->filterColumn('client_fullname', function($query, $keyword) {
                $sql = 'CONCAT(first_name COLLATE utf8mb4_unicode_ci, " ", COALESCE(last_name COLLATE utf8mb4_unicode_ci, "")) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('program_name', function($query, $keyword) {
                $sql = 'CONCAT(prog_program COLLATE utf8mb4_unicode_ci, " - ", COALESCE(tbl_main_prog.prog_name COLLATE utf8mb4_unicode_ci, ""), " / ", COALESCE(tbl_sub_prog.sub_prog_name COLLATE utf8mb4_unicode_ci, "")) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->rawColumns(['refund_notes'])
            ->make(true);
    }

    public function getRefundById($refundId)
    {
        return Refund::find($refundId);
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

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
                $query_b2c = Receipt::leftJoin('tbl_inv', 'tbl_inv.inv_id', '=', 'tbl_receipt.inv_id')
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

                # query for school program
                // $query_b2b_school = Receipt::leftJoin('tbl_invb2b', 'tbl_invb2b.invb2b_id', '=', 'tbl_receipt.invb2b_id')
                //             ->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
                //             ->leftJoin('users', 'users.id', '=', 'tbl_sch_prog.empl_id')
                //             ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_sch_prog.prog_id')
                //             ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
                //             ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                //             ->leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_sch_prog.sch_id')
                //             ->leftJoin('users', 'users.id', '=', 'tbl_sch_prog.empl_id')
                //             ->whereNotIn('tbl_invb2b.invb2b_id', Refund::pluck('invb2b_id'))
                //             ->where('tbl_sch_prog.status', 3)
                //             ->select([
                //                 'tbl_sch_prog.id',
                //                 'tbl_sch_prog.refund_date',
                //                 'tbl_inv.inv_totalprice_idr as total_price',
                //                 DB::raw('(SELECT SUM(re.receipt_amount_idr) FROM tbl_receipt re WHERE re.inv_id = tbl_inv.inv_id) as total_paid'),
                //                 'tbl_client_prog.refund_notes',
                //                 'tbl_reason.reason_name as refund_reason',
                //                 DB::raw('CONCAT(users.first_name, " ", COALESCE(users.last_name, "")) as pic_name'),
                //                 'tbl_inv.clientprog_id',
                //                 'tbl_receipt.id',
                //                 DB::raw('CONCAT(tbl_client.first_name, " ", COALESCE(tbl_client.last_name, "")) as client_fullname'),
                //                 DB::raw('CONCAT(prog_program, " - ", COALESCE(tbl_main_prog.prog_name, ""), " / ", COALESCE(tbl_sub_prog.sub_prog_name, "")) as program_name'),                        
                                
                //             ]);

                # query for partner program
                // $query_b2b_partner = Receipt::leftJoin('tbl_invb2b', 'tbl_invb2b.invb2b_id', '=', 'tbl_receipt.invb2b_id')
                //             ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
                //             ->leftJoin('users', 'users.id', '=', 'tbl_partner_prog.empl_id')
                //             ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_partner_prog.prog_id')
                //             ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
                //             ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                //             ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')
                //             ->leftJoin('users', 'users.id', '=', 'tbl_partner_prog.empl_id')
                //             ->whereNotIn('tbl_invb2b.invb2b_id', Refund::pluck('invb2b_id'))
                //             ->where('tbl_sch_prog.status', 3)
                //             ->select([
                //                 'tbl_client_prog.clientprog_id',
                //                 'tbl_client_prog.refund_date',
                //                 'tbl_inv.inv_totalprice_idr as total_price',
                //                 DB::raw('(SELECT SUM(re.receipt_amount_idr) FROM tbl_receipt re WHERE re.inv_id = tbl_inv.inv_id) as total_paid'),
                //                 'tbl_client_prog.refund_notes',
                //                 'tbl_reason.reason_name as refund_reason',
                //                 DB::raw('CONCAT(users.first_name, " ", COALESCE(users.last_name, "")) as pic_name'),
                //                 'tbl_inv.clientprog_id',
                //                 'tbl_receipt.id',
                //                 DB::raw('CONCAT(tbl_client.first_name, " ", COALESCE(tbl_client.last_name, "")) as client_fullname'),
                //                 DB::raw('CONCAT(prog_program, " - ", COALESCE(tbl_main_prog.prog_name, ""), " / ", COALESCE(tbl_sub_prog.sub_prog_name, "")) as program_name'),                        
                                
                //             ]);

;
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

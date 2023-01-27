<?php

namespace App\Repositories;

use App\Interfaces\ReceiptRepositoryInterface;
use App\Models\Invb2b;
use App\Models\Receipt;
use App\Models\Refund;
use DataTables;
use Illuminate\Support\Facades\DB;

class ReceiptRepository implements ReceiptRepositoryInterface
{

    public function getAllReceiptSchDataTables()
    {
        return Datatables::eloquent(
            Invb2b::rightJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
                ->leftJoin('tbl_sch', 'tbl_sch_prog.sch_id', '=', 'tbl_sch.sch_id')
                ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_sch_prog.prog_id')
                ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                ->leftJoin('tbl_receipt', 'tbl_receipt.invb2b_id', '=', 'tbl_invb2b.invb2b_id')
                ->select(
                    'tbl_receipt.id as increment_receipt',
                    'tbl_sch.sch_name as school_name',
                    // 'tbl_prog.prog_program as program_name',
                    DB::raw('(CASE
                        WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                        ELSE tbl_prog.prog_program
                    END) AS program_name'),
                    'tbl_receipt.receipt_id',
                    'tbl_receipt.invb2b_id',
                    'tbl_receipt.receipt_method',
                    'tbl_receipt.created_at',
                    'tbl_invb2b.invb2b_num',
                    'tbl_invb2b.currency',
                    'tbl_receipt.receipt_amount as total_price_other',
                    'tbl_receipt.receipt_amount_idr as total_price_idr',
                )
                ->where('tbl_receipt.receipt_status', 1)
        )->make(true);
    }

    public function getAllReceiptCorpDataTables()
    {
        return Datatables::eloquent(
            Invb2b::rightJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
                ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')
                ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_partner_prog.prog_id')
                ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                ->leftJoin('tbl_receipt', 'tbl_receipt.invb2b_id', '=', 'tbl_invb2b.invb2b_id')
                ->select(
                    'tbl_receipt.id as increment_receipt',
                    'tbl_corp.corp_name',
                    // 'tbl_prog.prog_program as program_name',
                    DB::raw('(CASE
                        WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                        ELSE tbl_prog.prog_program
                    END) AS program_name'),
                    'tbl_receipt.receipt_id',
                    'tbl_receipt.invb2b_id',
                    'tbl_receipt.receipt_method',
                    'tbl_receipt.created_at',
                    'tbl_invb2b.invb2b_num',
                    'tbl_invb2b.currency',
                    'tbl_receipt.receipt_amount as total_price_other',
                    'tbl_receipt.receipt_amount_idr as total_price_idr',
                )
                ->where('tbl_receipt.receipt_status', 1)
        )->make(true);
    }

    public function getReceiptById($receiptId)
    {
        return Receipt::find($receiptId);
    }


    public function getAllReceiptByStatusDataTables($status)
    {
        switch ($status) {

            case "list":
            case "refund-request":
            default:
                $query = Receipt::leftJoin('tbl_inv', 'tbl_inv.inv_id', '=', 'tbl_receipt.inv_id')
                    ->leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')
                    ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
                    ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
                    ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                    ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')
                    ->when($status == "list", function ($q) {
                        $q->where('receipt_status', 1);
                    })
                    ->when($status == "refund-request", function ($q) {
                        $q->where('tbl_client_prog.status', 3);
                    })
                    ->select([
                        'tbl_client_prog.clientprog_id',
                        'tbl_inv.clientprog_id',
                        'tbl_receipt.id',
                        'tbl_receipt.receipt_id',
                        'tbl_receipt.created_at',
                        DB::raw('CONCAT(first_name, " ", COALESCE(last_name, "")) as client_fullname'),
                        DB::raw('CONCAT(prog_program, " - ", COALESCE(tbl_main_prog.prog_name, ""), " / ", COALESCE(tbl_sub_prog.sub_prog_name, "")) as program_name'),
                        'tbl_inv.inv_id',
                        'tbl_receipt.receipt_method',
                        'tbl_inv.created_at',
                        'tbl_inv.inv_duedate',
                        'tbl_receipt.receipt_amount_idr',
                        DB::raw('DATEDIFF(inv_duedate, now()) as date_difference')
                    ]);
                break;

            case "refund-list":
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
                        'tbl_receipt.receipt_id',
                        'tbl_receipt.created_at',
                        DB::raw('CONCAT(first_name COLLATE utf8mb4_unicode_ci, " ", COALESCE(last_name COLLATE utf8mb4_unicode_ci, "")) as client_fullname'),
                        DB::raw('CONCAT(prog_program COLLATE utf8mb4_unicode_ci, " - ", COALESCE(tbl_main_prog.prog_name COLLATE utf8mb4_unicode_ci, ""), " / ", COALESCE(tbl_sub_prog.sub_prog_name COLLATE utf8mb4_unicode_ci, "")) as program_name'),
                        'tbl_inv.inv_id',
                        'tbl_receipt.receipt_method',
                        'tbl_inv.created_at',
                        'tbl_inv.inv_duedate',
                        'tbl_receipt.receipt_amount_idr',
                        DB::raw('DATEDIFF(inv_duedate, now()) as date_difference')
                    ]);
                break;
        }

        return DataTables::eloquent($query)
            ->filterColumn('client_fullname', function ($query, $keyword) {
                $sql = 'CONCAT(first_name COLLATE utf8mb4_unicode_ci, " ", COALESCE(last_name COLLATE utf8mb4_unicode_ci, "")) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('program_name', function ($query, $keyword) {
                $sql = 'CONCAT(prog_program COLLATE utf8mb4_unicode_ci, " - ", COALESCE(tbl_main_prog.prog_name COLLATE utf8mb4_unicode_ci, ""), " / ", COALESCE(tbl_sub_prog.sub_prog_name COLLATE utf8mb4_unicode_ci, "")) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })->make(true);
    }

    public function getReceiptByInvoiceIdentifier($invoiceType, $identifier)
    {
        return Receipt::when($invoiceType == "Program", function ($query) use ($identifier) {
            $query->where('inv_id', $identifier);
        })->when($invoiceType == "Installment", function ($query) use ($identifier) {
            $query->where('invdtl_id', $identifier);
        })->when($invoiceType == "B2B", function ($query) use ($identifier) {
            $query->where('invb2b_id', $identifier);
        })->first();
    }

    public function createReceipt(array $receiptDetails)
    {
        return Receipt::create($receiptDetails);
    }

    public function updateReceipt($receiptId, array $newDetails)
    {
        return Receipt::whereId($receiptId)->update($newDetails);
    }

    public function updateReceiptByInvoiceIdentifier($invoiceType, $identifier, array $newDetails)
    {
        return Receipt::when($invoiceType == "Program", function ($query) use ($identifier, $newDetails) {
            $query->where('inv_id', $identifier)->update($newDetails);
        })->when($invoiceType == "Installment", function ($query) use ($identifier, $newDetails) {
            $query->where('invdtl_id', $identifier)->update($newDetails);
        })->when($invoiceType == "B2B", function ($query) use ($identifier, $newDetails) {
            $query->where('invb2b_id', $identifier)->update($newDetails);
        });
    }

    public function deleteReceipt($receiptId)
    {
        return Receipt::whereId($receiptId)->delete();
    }
}

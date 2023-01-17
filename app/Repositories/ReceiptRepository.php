<?php

namespace App\Repositories;

use App\Interfaces\ReceiptRepositoryInterface;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReceiptRepository implements ReceiptRepositoryInterface
{
    public function getAllReceiptByStatusDataTables($status)
    {
        return DataTables::eloquent(
            Receipt::leftJoin('tbl_inv', 'tbl_inv.inv_id', '=', 'tbl_receipt.inv_id')
            ->leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')
            ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
            ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
            ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
            ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')
            ->when($status == 'list', function($query) {
                $query->where('receipt_status', 1);
            })->when($status == "refund-request", function($query) {
                $query->where('receipt_status', 2)->whereNull('total_refunded');
            })->when($status == "refund-list", function ($query) {
                $query->where('receipt_status', 2)->whereNotNull('total_refunded');
            })
            ->select([
                'tbl_inv.clientprog_id',
                'tbl_receipt.id',
                'tbl_receipt.receipt_id',
                'tbl_receipt.created_at',
                DB::raw('CONCAT(first_name, " ", last_name) as client_fullname'),
                DB::raw('CONCAT(prog_program, " - ", tbl_main_prog.prog_name, " / ", tbl_sub_prog.sub_prog_name) as program_name'),
                'tbl_inv.inv_id',
                'tbl_receipt.receipt_method',
                'tbl_inv.created_at',
                'tbl_inv.inv_duedate',
                'tbl_receipt.receipt_amount_idr',
                DB::raw('DATEDIFF(inv_duedate, now()) as date_difference')
            ])
        )->make(true);
    }

    public function getReceiptByInvoiceIdentifier($invoiceType, $identifier)
    {
        return Receipt::when($invoiceType == "Program", function($query) use ($identifier) {
            $query->where('inv_id', $identifier);
        })->when($invoiceType == "Installment", function($query) use ($identifier) {
            $query->where('invdtl_id', $identifier);
        })->when($invoiceType == "B2B", function ($query) use ($identifier) {
            $query->where('invb2b_id', $identifier);
        })->first();
    }

    public function getReceiptById($receiptId)
    {
        return Receipt::find($receiptId);
    }

    public function createReceipt(array $receiptDetails)
    {
        return Receipt::create($receiptDetails);
    }

    public function updateReceipt($receiptId, array $newDetails)
    {
        return Receipt::whereId($receiptId)->update($newDetails);
    }

    public function deleteReceipt($receiptId)
    {
        return Receipt::whereId($receiptId)->delete();
    }
}

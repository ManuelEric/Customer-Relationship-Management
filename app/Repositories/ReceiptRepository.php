<?php

namespace App\Repositories;

use App\Interfaces\ReceiptRepositoryInterface;
use App\Models\Invb2b;
use App\Models\Receipt;
use DataTables;
use Illuminate\Support\Facades\DB;

class ReceiptRepository implements ReceiptRepositoryInterface
{

    public function getAllReceiptSchDataTables()
    {
        return datatables::eloquent(
            Invb2b::leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
                ->leftJoin('tbl_sch', 'tbl_sch_prog.sch_id', '=', 'tbl_sch.sch_id')
                ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_sch_prog.prog_id')
                ->leftJoin('tbl_receipt', 'tbl_receipt.invb2b_id', '=', 'tbl_invb2b.invb2b_id')
                // ->groupBy('tbl_receipt.invb2b_id')

                ->select(
                    'tbl_receipt.id',
                    'tbl_sch.sch_name as school_name',
                    'tbl_prog.prog_program as program_name',
                    'tbl_receipt.receipt_id',
                    'tbl_receipt.invb2b_id',
                    'tbl_receipt.receipt_method',
                    'tbl_receipt.created_at',
                    'tbl_invb2b.invb2b_num',
                    'tbl_receipt.receipt_amount_idr as total_price_idr'
                    // DB::raw('SUM(tbl_receipt.receipt_amount_idr) as total_price_idr'),
                )
                ->where('tbl_receipt.receipt_status', 1)
            // ->groupBy('tbl_receipt.invb2b_id')
            // ->sum('tbl_receipt.receipt_amount_idr')
        )->make(true);
    }

    public function getReceiptById($receiptId)
    {
        return Receipt::find($receiptId);
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

    public function deleteReceipt($receiptId)
    {
        return Receipt::whereId($receiptId)->delete();
    }
}

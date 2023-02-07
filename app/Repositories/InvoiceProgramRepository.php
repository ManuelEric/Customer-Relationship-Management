<?php

namespace App\Repositories;

use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Models\ClientProgram;
use App\Models\InvoiceProgram;
use App\Models\ViewClientProgram;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\DB;

class InvoiceProgramRepository implements InvoiceProgramRepositoryInterface
{
    public function getAllInvoiceProgramDataTables($status)
    {
        switch ($status) {

            case "needed":
                $query = ViewClientProgram::when($status == "needed", function ($q) {
                    # select all client program
                    # where status already success which mean they(client) already paid the program
                    $q->doesntHave('invoice')->where('status', 1);
                });
                break;

            case "list":
                $query = InvoiceProgram::leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')
                    ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
                    ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
                    ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                    ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')
                    ->select([
                        'tbl_inv.clientprog_id',
                        DB::raw('CONCAT(first_name, " ", COALESCE(last_name, "")) as client_fullname'),
                        DB::raw('CONCAT(prog_program, " - ", COALESCE(tbl_main_prog.prog_name, ""), " / ", COALESCE(tbl_sub_prog.sub_prog_name, "")) as program_name'),
                        'inv_id',
                        'inv_paymentmethod',
                        'tbl_inv.created_at',
                        'inv_duedate',
                        'inv_totalprice_idr',
                    ]);
                break;

            case "reminder":
                // $query = InvoiceProgram::leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')
                //         ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
                //         ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
                //         ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                //         ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')
                //         ->select([
                //             'tbl_inv.clientprog_id',
                //             DB::raw('CONCAT(first_name, " ", last_name) as client_fullname'),
                //             DB::raw('CONCAT(prog_program, " - ", tbl_main_prog.prog_name, " / ", tbl_sub_prog.sub_prog_name) as program_name'),
                //             'inv_id',
                //             'inv_paymentmethod',
                //             'tbl_inv.created_at',
                //             'inv_duedate',
                //             'inv_totalprice_idr',
                //             DB::raw('DATEDIFF(inv_duedate, now()) as date_difference')
                //         ])
                //         ->orderBy('date_difference', 'asc');
                $query = InvoiceProgram::leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')
                    ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
                    ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
                    ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                    ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')
                    ->select([
                        'tbl_inv.clientprog_id',
                        DB::raw('CONCAT(first_name, " ", COALESCE(last_name, "")) as client_fullname'),
                        DB::raw('CONCAT(prog_program, " - ", COALESCE(tbl_main_prog.prog_name, ""), " / ", COALESCE(tbl_sub_prog.sub_prog_name, "")) as program_name'),
                        'inv_id',
                        'inv_paymentmethod',
                        'tbl_inv.created_at',
                        'inv_duedate',
                        'inv_totalprice_idr',
                        DB::raw('DATEDIFF(inv_duedate, now()) as date_difference')
                    ])
                    ->where(DB::raw('DATEDIFF(inv_duedate, now())'), '<=', 7)
                    ->orderBy('date_difference', 'asc');
                break;
        }


        return DataTables::eloquent($query)->make(true);
    }

    public function getInvoiceByClientProgId($clientProgId)
    {
        return InvoiceProgram::where('clientprog_id', $clientProgId)->first();
    }

    public function createInvoice(array $invoiceDetails)
    {
        return InvoiceProgram::create($invoiceDetails);
    }

    public function updateInvoice($invoiceId, array $invoiceDetails)
    {
        unset($invoiceDetails['is_session']);
        unset($invoiceDetails['invoice_date']);
        return InvoiceProgram::where('inv_id', $invoiceId)->update($invoiceDetails);
    }

    public function deleteInvoiceByClientProgId($clientProgId)
    {
        return InvoiceProgram::where('clientprog_id', $clientProgId)->delete();
    }

    public function getReportInvoiceB2c($start_date = null, $end_date = null, $whereBy)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        // $invb2c = InvoiceProgram::leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')
        //     ->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_client_prog.prog_id')
        //     ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
        //     ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
        //     ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')
        //     ->select(
        //         'tbl_inv.inv_id',
        //         DB::raw('CONCAT(first_name, " ", COALESCE(last_name, "")) as client_name'),
        //          DB::raw('(CASE
        //             WHEN tbl_inv.id > 0 THEN "B2C"
        //         END) AS type'),
        //         DB::raw('(CASE
        //             WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
        //             ELSE tbl_prog.prog_program
        //         END) AS program_name'),
        //         'tbl_inv.inv_paymentmethod',
        //         'tbl_inv.inv_duedate',
        //         'tbl_inv.inv_totalprice_idr',
        //     );

        if (isset($start_date) && isset($end_date)) {
            return InvoiceProgram::whereDate($whereBy, '>=', $start_date)
                ->whereDate($whereBy, '<=', $end_date)
                ->get();
        } else if (isset($start_date) && !isset($end_date)) {
            return InvoiceProgram::whereDate($whereBy, '>=', $start_date)
                ->get();
        } else if (!isset($start_date) && isset($end_date)) {
            return InvoiceProgram::whereDate($whereBy, '<=', $end_date)
                ->get();
        } else {
            return InvoiceProgram::whereBetween($whereBy, [$firstDay, $lastDay])
                ->get();
        }
    }


    public function getReportUnpaidInvoiceB2c($start_date = null, $end_date = null)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        $invoiceB2c = InvoiceProgram::leftJoin('tbl_invdtl', 'tbl_invdtl.inv_id', '=', 'tbl_inv.inv_id')
            ->leftJoin(
                'tbl_receipt',
                DB::raw('(CASE
                        WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN 
                            tbl_receipt.inv_id 
                        WHEN tbl_inv.inv_paymentmethod = "Installment" THEN 
                                tbl_receipt.invdtl_id
                        ELSE null
                    END )'),
                DB::raw('CASE
                        WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN 
                            tbl_inv.inv_id 
                        WHEN tbl_inv.inv_paymentmethod = "Installment" THEN 
                                tbl_invdtl.invdtl_id
                        ELSE null
                    END')
            )

            ->select(
                'tbl_inv.inv_id',
                'tbl_inv.clientprog_id',
                'tbl_inv.inv_duedate',
                'tbl_receipt.receipt_id',
                'tbl_receipt.receipt_amount_idr',
                'tbl_receipt.created_at as paid_date',
                'tbl_invdtl.invdtl_installment',
                'tbl_invdtl.invdtl_id',
            );

        if (isset($start_date) && isset($end_date)) {
            return $invoiceB2c->whereBetween('inv_duedate', [$start_date, $end_date])
                ->orderBy('inv_id', 'asc')
                ->orderBy('invdtl_id', 'asc')
                ->get();
        } else if (isset($start_date) && !isset($end_date)) {
            return $invoiceB2c->whereDate('inv_duedate', '>=', $start_date)
                ->orderBy('inv_id', 'asc')
                ->orderBy('invdtl_id', 'asc')
                ->get();
        } else if (!isset($start_date) && isset($end_date)) {
            return $invoiceB2c->whereDate('inv_duedate', '<=', $end_date)
                ->orderBy('inv_id', 'asc')
                ->orderBy('invdtl_id', 'asc')
                ->get();
        } else {
            return $invoiceB2c->whereBetween('inv_duedate', [$firstDay, $lastDay])
                ->orderBy('inv_id', 'asc')
                ->orderBy('invdtl_id', 'asc')
                ->get();
        }
    }
}

<?php

namespace App\Repositories;

use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Models\ClientProgram;
use App\Models\InvoiceProgram;
use App\Models\ViewClientProgram;
use DataTables;
use Illuminate\Support\Facades\DB;

class InvoiceProgramRepository implements InvoiceProgramRepositoryInterface 
{
    public function getAllInvoiceProgramDataTables($status)
    {
        switch($status) {

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
                            DB::raw('CONCAT(first_name, " ", last_name) as client_fullname'),
                            DB::raw('CONCAT(prog_program, " - ", tbl_main_prog.prog_name, " / ", tbl_sub_prog.sub_prog_name) as program_name'),
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
                            DB::raw('CONCAT(first_name, " ", last_name) as client_fullname'),
                            DB::raw('CONCAT(prog_program, " - ", tbl_main_prog.prog_name, " / ", tbl_sub_prog.sub_prog_name) as program_name'),
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

}
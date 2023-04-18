<?php

namespace App\Repositories;

use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Models\ClientProgram;
use App\Models\InvoiceProgram;
use App\Models\v1\Invoice as CRMInvoice;
use App\Models\ViewClientProgram;
use DataTables;
use Illuminate\Support\Carbon;
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
                })
                    ->orderBy('updated_at', 'desc')
                    ->orderBy('statusprog_date', 'desc');
                break;

            case "list":
                $query = InvoiceProgram::leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')
                    ->leftJoin('program', 'program.prog_id', '=', 'tbl_client_prog.prog_id')
                    // ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
                    // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                    ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')
                    ->select([
                        'tbl_inv.clientprog_id',
                        DB::raw('CONCAT(first_name, " ", COALESCE(last_name, "")) as client_fullname'),
                        'inv_id',
                        'inv_paymentmethod',
                        'tbl_inv.created_at',
                        'inv_duedate',
                        'inv_totalprice_idr',
                        'program.program_name'
                    ])->orderBy('tbl_inv.updated_at', 'desc');
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
                    ->leftJoin('program', 'program.prog_id', '=', 'tbl_client_prog.prog_id')
                    // ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
                    // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                    ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')
                    ->select([
                        'tbl_inv.clientprog_id',
                        DB::raw('CONCAT(first_name, " ", COALESCE(last_name, "")) as client_fullname'),
                        'program.program_name',
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

    public function getAllInvoiceProgram()
    {
        return InvoiceProgram::all();
    }

    public function getInvoiceByClientProgId($clientProgId)
    {
        return InvoiceProgram::where('clientprog_id', $clientProgId)->first();
    }

    public function getInvoiceByInvoiceId($invoiceId)
    {
        return InvoiceProgram::where('inv_id', $invoiceId)->first();
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

    public function getReportInvoiceB2c($start_date = null, $end_date = null)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        $queryInv = InvoiceProgram::whereRelation('clientprog', 'status', 1);

        if (isset($start_date) && isset($end_date)) {
            $queryInv->whereDate('tbl_inv.created_at', '>=', $start_date)
                ->whereDate('tbl_inv.created_at', '<=', $end_date);
        } else if (isset($start_date) && !isset($end_date)) {
            $queryInv->whereDate('tbl_inv.created_at', '>=', $start_date);
        } else if (!isset($start_date) && isset($end_date)) {
            $queryInv->whereDate('tbl_inv.created_at', '<=', $end_date);
        } else {
            $queryInv->whereBetween('tbl_inv.created_at', [$firstDay, $lastDay]);
        }

        return $queryInv->orderBy('tbl_inv.created_at', 'DESC')->withCount('invoiceDetail')->get();
    }


    public function getReportUnpaidInvoiceB2c($start_date = null, $end_date = null)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        $whereBy = DB::raw('(CASE 
                            WHEN tbl_receipt.id is not null THEN
                                tbl_receipt.created_at
                            ELSE 
                                (CASE 
                                    WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN 
                                        tbl_inv.inv_duedate 
                                    WHEN tbl_inv.inv_paymentmethod = "Installment" THEN 
                                        tbl_invdtl.invdtl_duedate
                                END)
                        END)');

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
                'tbl_inv.inv_duedate as invoice_duedate',
                'tbl_invdtl.invdtl_duedate as installment_duedate',
                DB::raw('(CASE
                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN 
                                tbl_inv.inv_totalprice_idr 
                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN 
                                tbl_invdtl.invdtl_amountidr
                            ELSE null
                        END) as total_price_inv'),
                'tbl_receipt.receipt_id',
                'tbl_receipt.receipt_amount_idr',
                'tbl_receipt.created_at as paid_date',
                'tbl_invdtl.invdtl_installment',
                'tbl_invdtl.invdtl_id',
            )
            ->whereRelation('clientprog', 'status', 1);

        if (isset($start_date) && isset($end_date)) {
            return $invoiceB2c->whereBetween($whereBy, [$start_date, $end_date])
                ->orderBy('inv_id', 'asc')
                ->orderBy('invdtl_id', 'asc')
                ->get();
        } else if (isset($start_date) && !isset($end_date)) {
            return $invoiceB2c->whereDate($whereBy, '>=', $start_date)
                ->orderBy('inv_id', 'asc')
                ->orderBy('invdtl_id', 'asc')
                ->get();
        } else if (!isset($start_date) && isset($end_date)) {
            return $invoiceB2c->whereDate($whereBy, '<=', $end_date)
                ->orderBy('inv_id', 'asc')
                ->orderBy('invdtl_id', 'asc')
                ->get();
        } else {
            return $invoiceB2c->whereBetween($whereBy, [$firstDay, $lastDay])
                ->orderBy('inv_id', 'asc')
                ->orderBy('invdtl_id', 'asc')
                ->get();
        }
    }

    public function getTotalInvoiceNeeded($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        return ViewClientProgram::doesntHave('invoice')
            ->select(DB::raw('COUNT(clientprog_id) as count_invoice_needed'))
            ->where('status', 1)
            ->whereYear('success_date', '=', $year)
            ->whereMonth('success_date', '=', $month)
            ->get();
    }

    public function getTotalInvoice($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        $whereBy = DB::raw('(CASE
                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN 
                                tbl_inv.inv_duedate 
                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN 
                                tbl_invdtl.invdtl_duedate
                        END)');

        return InvoiceProgram::leftJoin('tbl_invdtl', 'tbl_invdtl.inv_id', '=', 'tbl_inv.inv_id')
            ->leftJoin('clientprogram', 'clientprogram.clientprog_id', '=', 'tbl_inv.clientprog_id')
            ->select(
                'tbl_inv.id',
                'tbl_invdtl.invdtl_id',
                'tbl_inv.inv_totalprice_idr',
                'tbl_inv.inv_paymentmethod',
                'tbl_invdtl.invdtl_amountidr'
            )->whereYear($whereBy, '=', $year)
            ->whereMonth($whereBy, '=', $month)
            ->where('clientprogram.status', 1)
            ->get();
    }

    public function getTotalRefundRequest($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        return ViewClientProgram::leftJoin('tbl_inv', 'tbl_inv.clientprog_id', '=', 'clientprogram.clientprog_id')
            ->select(DB::raw("count('clientprogram.clientprog_id') as count_refund_request"))
            ->where('clientprogram.status', 3)
            ->where('tbl_inv.inv_status', 1)
            ->whereYear('clientprogram.refund_date', '=', $year)
            ->whereMonth('clientprogram.refund_date', '=', $month)
            ->get();
    }

    public function getInvoiceOutstandingPayment($monthYear, $type, $start_date = null, $end_date = null)
    {
        $whereBy = DB::raw('(CASE
                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN 
                                tbl_inv.inv_duedate 
                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN 
                                tbl_invdtl.invdtl_duedate
                        END)');

        if (isset($monthYear)) {
            $year = date('Y', strtotime($monthYear));
            $month = date('m', strtotime($monthYear));
        }

        $queryInv = InvoiceProgram::leftJoin('tbl_invdtl', 'tbl_invdtl.inv_id', '=', 'tbl_inv.inv_id')
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
            ->leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')
            ->leftJoin('program', 'program.prog_id', '=', 'tbl_client_prog.prog_id')
            // ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
            // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
            ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id');

        switch ($type) {
            case 'paid':
                $queryInv->select([
                    'tbl_inv.inv_id as invoice_id',
                    'tbl_inv.clientprog_id',
                    DB::raw('CONCAT(first_name, " ", COALESCE(last_name, "")) as full_name'),
                    'program.program_name',
                    // DB::raw('CONCAT(prog_program, " - ", COALESCE(tbl_main_prog.prog_name, ""), COALESCE(CONCAT(" / ", tbl_sub_prog.sub_prog_name), "")) as program_name'),
                    'tbl_inv.inv_totalprice_idr as total_price_inv',
                    'tbl_invdtl.invdtl_installment as installment_name',
                    DB::raw("'B2C' as type"),
                    'tbl_receipt.receipt_amount_idr as total'
                ])->whereNotNull('tbl_receipt.id');

                if (isset($monthYear)) {
                    $queryInv->whereYear('tbl_receipt.created_at', '=', $year)
                        ->whereMonth('tbl_receipt.created_at', '=', $month);
                } else {
                    $queryInv->whereBetween('tbl_receipt.created_at', [$start_date, $end_date]);
                }
                break;

            case 'unpaid':
                $queryInv->select([
                    'tbl_inv.inv_id as invoice_id',
                    'tbl_inv.clientprog_id',
                    DB::raw('CONCAT(first_name, " ", COALESCE(last_name, "")) as full_name'),
                    'program.program_name',
                    // DB::raw('CONCAT(prog_program, " - ", COALESCE(tbl_main_prog.prog_name, ""), COALESCE(CONCAT(" / ", tbl_sub_prog.sub_prog_name), "")) as program_name'),
                    'tbl_invdtl.invdtl_installment as installment_name',
                    DB::raw("'B2C' as type"),
                    DB::raw('(CASE
                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN 
                                tbl_inv.inv_totalprice_idr 
                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN 
                                tbl_invdtl.invdtl_amountidr
                            ELSE null
                        END) as total')
                ])->whereNull('tbl_receipt.id');

                if (isset($monthYear)) {
                    $queryInv->whereYear($whereBy, '=', $year)
                        ->whereMonth($whereBy, '=', $month);
                } else {
                    $queryInv->whereBetween($whereBy, [$start_date, $end_date]);
                }
                break;
        }

        $queryInv
            ->whereRelation('clientprog', 'status', 1);
        // ->groupBy('tbl_inv.inv_id');

        return $queryInv->get();
    }

    public function getRevenueByYear($year)
    {
        return InvoiceProgram::leftJoin('tbl_invdtl', 'tbl_invdtl.inv_id', '=', 'tbl_inv.inv_id')
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
            ->select(DB::raw('SUM(tbl_receipt.receipt_amount_idr) as total'), DB::raw('MONTH(tbl_receipt.created_at) as month'))
            ->whereYear('tbl_receipt.created_at', '=', $year)
            ->whereRelation('clientprog', 'status', 1)
            ->whereNotNull('tbl_receipt.id')
            ->groupBy(DB::raw('MONTH(tbl_receipt.created_at)'))
            ->get();
    }

    public function getInvoiceDifferences()
    {
        $invoice_v2 = InvoiceProgram::pluck('inv_id')->toArray();

        return CRMInvoice::whereNotIn('inv_id', $invoice_v2)->get();
    }
}

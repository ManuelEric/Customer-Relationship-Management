<?php

namespace App\Repositories;

use App\Interfaces\ReceiptRepositoryInterface;
use App\Models\Invb2b;
use App\Models\Receipt;
use App\Models\v1\Receipt as V1Receipt;
use App\Models\Refund;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\DB;

class ReceiptRepository implements ReceiptRepositoryInterface
{

    public function getAllReceiptSchDataTables()
    {
        return Datatables::eloquent(
            Receipt::leftJoin('tbl_invdtl', 'tbl_invdtl.invdtl_id', '=', 'tbl_receipt.invdtl_id')
                ->leftJoin('tbl_invb2b', 'tbl_invb2b.invb2b_id', '=', DB::raw('(CASE WHEN tbl_receipt.invdtl_id is not null THEN tbl_invdtl.invb2b_id ELSE tbl_receipt.invb2b_id END)'))
                ->rightJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
                ->leftJoin('tbl_sch', 'tbl_sch_prog.sch_id', '=', 'tbl_sch.sch_id')
                ->leftJoin('program', 'program.prog_id', '=', 'tbl_sch_prog.prog_id')
                // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                ->select(
                    'tbl_receipt.id as increment_receipt',
                    'tbl_sch.sch_name as school_name',
                    // 'tbl_prog.prog_program as program_name',
                    'program.program_name',
                    'tbl_receipt.receipt_id',
                    'tbl_invb2b.invb2b_id',
                    'tbl_receipt.receipt_method',
                    'tbl_receipt.created_at',
                    'tbl_invb2b.invb2b_num',
                    'tbl_invb2b.currency',
                    'tbl_receipt.receipt_amount as total_price_other',
                    'tbl_receipt.receipt_amount_idr as total_price_idr',
                )
                ->where('tbl_receipt.receipt_status', 1)
                // ->orderBy('tbl_receipt.created_at', 'DESC')
        )->make(true);
    }

    public function getAllReceiptCorpDataTables()
    {
        return Datatables::eloquent(
            Receipt::leftJoin('tbl_invdtl', 'tbl_invdtl.invdtl_id', '=', 'tbl_receipt.invdtl_id')
                ->leftJoin('tbl_invb2b', 'tbl_invb2b.invb2b_id', '=', DB::raw('(CASE WHEN tbl_receipt.invdtl_id is not null THEN tbl_invdtl.invb2b_id ELSE tbl_receipt.invb2b_id END)'))
                ->rightJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
                ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_partner_prog.corp_id')
                ->leftJoin('program', 'program.prog_id', '=', 'tbl_partner_prog.prog_id')
                // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
                ->select(
                    'tbl_receipt.id as increment_receipt',
                    'tbl_corp.corp_name',
                    // 'tbl_prog.prog_program as program_name',
                    'program.program_name',
                    'tbl_receipt.receipt_id',
                    'tbl_invb2b.invb2b_id',
                    'tbl_receipt.receipt_method',
                    'tbl_receipt.created_at',
                    'tbl_invb2b.invb2b_num',
                    'tbl_invb2b.currency',
                    'tbl_receipt.receipt_amount as total_price_other',
                    'tbl_receipt.receipt_amount_idr as total_price_idr',
                )
                ->where('tbl_receipt.receipt_status', 1)
                // ->orderBy('tbl_receipt.created_at', 'DESC')
        )->make(true);
    }

    public function getAllReceiptReferralDataTables()
    {
        return Datatables::eloquent(
            Receipt::leftJoin('tbl_invb2b', 'tbl_invb2b.invb2b_id', '=', 'tbl_receipt.invb2b_id')
                ->rightJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')
                ->leftJoin('tbl_corp', 'tbl_corp.corp_id', '=', 'tbl_referral.partner_id')
                ->select(
                    'tbl_receipt.id as increment_receipt',
                    'tbl_corp.corp_name',
                    'tbl_referral.additional_prog_name as program_name',
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
                ->where('tbl_referral.referral_type', 'Out')
                // ->orderBy('tbl_receipt.created_at', 'DESC')
        )->make(true);
    }

    public function getReceiptById($receiptId)
    {
        return Receipt::find($receiptId);
    }


    public function getAllReceiptByStatusDataTables($status = null) # client program
    {
        if($status){
            $fColumns = [
                'relation' => ['invoiceProgram', 'invoiceProgram.bundling.first_detail.client_program.client', 'invoiceProgram.bundling.first_detail.client_program.program'],
                'columns' => ['created_at', 'client_fullname', 'program_name'],
                'realColumns' => ['created_at', DB::raw('CONCAT(first_name COLLATE utf8mb4_unicode_ci, " ", COALESCE(last_name COLLATE utf8mb4_unicode_ci, ""))'), 'prog_program']
            ];
            $query = Receipt::where('receipt_status', 1)
                            ->whereNotNull('inv_id')
                            ->whereRelation('invoiceProgram', 'bundling_id', '!=', null)
                            ->select([
                                'id',
                                'receipt_id',
                                'inv_id',
                                'receipt_method',
                                'receipt_amount_idr',
                            ]);
            
            $datatable = DataTables::eloquent($query)
            ->addColumn('created_at', function($query){
                return $query->invoiceProgram->created_at;
            })
            ->addColumn('bundling_id', function($query){
                return $query->invoiceProgram->bundling_id;
            })
            ->addColumn('client_fullname', function($query){
                return $query->invoiceProgram->bundling->details->first()->client_program->client->full_name;
            })
            ->addColumn('program_name', function($query){
                return $query->invoiceProgram->bundling->details->first()->client_program->program->program_name;
            });

            foreach ($fColumns['columns'] as $key => $column) {
                $datatable->filterColumn($column, function ($query, $keyword) use($fColumns, $key) {
                    $query->whereRelation($fColumns['relation'][$key], $fColumns['realColumns'][$key], 'like', "%{$keyword}%");
                });
               
            }

            return $datatable->make(true);;

        }

        $query = Receipt::leftJoin('tbl_inv', 'tbl_inv.inv_id', '=', 'tbl_receipt.inv_id')
            ->leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')
            ->leftJoin('program', 'program.prog_id', '=', 'tbl_client_prog.prog_id')
            // ->leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')
            // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
            ->leftJoin('tbl_client', 'tbl_client.id', '=', 'tbl_client_prog.client_id')
            ->where('receipt_status', 1)
            ->whereNotNull('tbl_receipt.inv_id')
            ->whereRelation('invoiceProgram', 'bundling_id', null)
            ->select([
                'tbl_client_prog.clientprog_id',
                // 'tbl_inv.clientprog_id',
                'tbl_receipt.id',
                'tbl_receipt.receipt_id',
                // 'tbl_receipt.created_at',
                DB::raw('CONCAT(first_name, " ", COALESCE(last_name, "")) as client_fullname'),
                'program.program_name',
                // DB::raw('CONCAT(prog_program, " - ", COALESCE(tbl_main_prog.prog_name, ""), " / ", COALESCE(tbl_sub_prog.sub_prog_name, "")) as program_name'),
                'tbl_inv.inv_id',
                'tbl_receipt.receipt_method',
                'tbl_inv.created_at',
                'tbl_inv.inv_duedate',
                'tbl_receipt.receipt_amount_idr',
                DB::raw('DATEDIFF(inv_duedate, now()) as date_difference')
            ]);
            
            // ->orderBy('tbl_receipt.created_at', 'DESC');

        return DataTables::eloquent($query)
            ->addColumn('is_bundle', function ($query) {
                return $query->invoiceProgram->clientprog->bundlingDetail()->count();
            })
            ->addColumn('bundling_id', function ($query) {
                return $query->invoiceProgram->clientprog->bundlingDetail()->count() > 0 ? $query->invoiceProgram->clientprog->bundlingDetail->bundling_id : null;
            })
            ->filterColumn('client_fullname', function ($query, $keyword) {
                $sql = 'CONCAT(first_name COLLATE utf8mb4_unicode_ci, " ", COALESCE(last_name COLLATE utf8mb4_unicode_ci, "")) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->make(true);
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

    public function getReceiptByReceiptId($receiptId)
    {
        return Receipt::where('receipt_id', $receiptId)->first();
    }

    public function getAllReceiptSchool()
    {
        return Receipt::leftJoin('tbl_invdtl', 'tbl_invdtl.invdtl_id', '=', 'tbl_receipt.invdtl_id')
            ->leftJoin('tbl_invb2b', 'tbl_invb2b.invb2b_id', '=', DB::raw('(CASE WHEN tbl_receipt.invdtl_id is not null THEN tbl_invdtl.invb2b_id ELSE tbl_receipt.invb2b_id END)'))
            ->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->whereNotNull('tbl_sch_prog.id')
            ->get();
    }

    public function createReceipt(array $receiptDetails)
    {
        return Receipt::create($receiptDetails);
    }

    public function insertReceipt(array $receiptDetails)
    {
        return Receipt::insert($receiptDetails);
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

    public function getReportReceipt($start_date = null, $end_date = null)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        $queryReceipt = Receipt::leftJoin('tbl_invdtl', 'tbl_invdtl.invdtl_id', '=', 'tbl_receipt.invdtl_id')
            ->leftJoin('tbl_invb2b', 'tbl_invb2b.invb2b_id', '=', DB::raw('(CASE WHEN tbl_receipt.invdtl_id is not null THEN tbl_invdtl.invb2b_id ELSE tbl_receipt.invb2b_id END)'))
            ->leftJoin('tbl_inv', 'tbl_inv.inv_id', '=', DB::raw('(CASE WHEN tbl_receipt.invdtl_id is not null THEN tbl_invdtl.inv_id ELSE tbl_receipt.inv_id END)'))
            ->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')
            ->leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')
            ->select(
                'tbl_inv.bundling_id',
                'tbl_receipt.id',
                'tbl_receipt.receipt_id',
                'tbl_receipt.invdtl_id',
                'tbl_receipt.receipt_method',
                'tbl_invb2b.ref_id',
                'tbl_receipt.created_at',
                'tbl_receipt.receipt_amount_idr',
                'tbl_receipt.receipt_amount',
                DB::raw('(CASE
                            WHEN tbl_receipt.invb2b_id is not null THEN tbl_receipt.invb2b_id
                            WHEN tbl_receipt.invdtl_id is not null THEN 
                                (CASE
                                    WHEN tbl_invdtl.invb2b_id is not null THEN tbl_invdtl.invb2b_id
                                END)
                            END) as invb2b_id'),
                DB::raw('(CASE
                            WHEN tbl_receipt.inv_id is not null THEN tbl_receipt.inv_id
                            WHEN tbl_receipt.invdtl_id is not null THEN 
                                (CASE
                                    WHEN tbl_invdtl.inv_id is not null THEN tbl_invdtl.inv_id
                                END)
                            END) as inv_id'),
                DB::raw('(CASE
                            WHEN tbl_receipt.invdtl_id is not null THEN 
                            (CASE
                                WHEN tbl_invdtl.invb2b_id is not null THEN  
                                    (CASE 
                                        WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                                        WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                                    END)
                                WHEN tbl_invdtl.inv_id is not null THEN tbl_client_prog.status
                            END)
                            WHEN tbl_receipt.inv_id is not null THEN tbl_client_prog.status
                            WHEN tbl_invb2b.schprog_id is not null THEN tbl_sch_prog.status
                            WHEN tbl_invb2b.partnerprog_id is not null THEN tbl_partner_prog.status
                    END) as status_where'),
                DB::raw('(CASE
                            WHEN tbl_receipt.invdtl_id is not null THEN 
                            (CASE
                                WHEN tbl_invdtl.invb2b_id is not null THEN  
                                    (CASE 
                                        WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.referral_type
                                    END)
                            END)
                            WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.referral_type
                            ELSE NULL
                    END) as referral_type'),
            );

        $queryReceipt->whereNull('tbl_inv.bundling_id');

        if (isset($start_date) && isset($end_date)) {
            $queryReceipt->whereDate('tbl_receipt.created_at', '>=', $start_date)
                ->whereDate('tbl_receipt.created_at', '<=', $end_date)
                ->get();
        } else if (isset($start_date) && !isset($end_date)) {
            $queryReceipt->whereDate('tbl_receipt.created_at', '>=', $start_date)
                ->get();
        } else if (!isset($start_date) && isset($end_date)) {
            $queryReceipt->whereDate('tbl_receipt.created_at', '<=', $end_date)
                ->get();
        } else {
            $queryReceipt->whereBetween('tbl_receipt.created_at', [$firstDay, $lastDay])
                ->get();
        }

        return $queryReceipt->orderBy('tbl_receipt.receipt_id', 'ASC')->get();
    }

    public function getTotalReceipt($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        return Receipt::leftJoin('tbl_invdtl', 'tbl_invdtl.invdtl_id', '=', 'tbl_receipt.invdtl_id')
            ->leftJoin('tbl_invb2b', 'tbl_invb2b.invb2b_id', '=', DB::raw('(CASE WHEN tbl_receipt.invdtl_id is not null THEN tbl_invdtl.invb2b_id ELSE tbl_receipt.invb2b_id END)'))
            ->leftJoin('tbl_inv', 'tbl_inv.inv_id', '=', DB::raw('(CASE WHEN tbl_receipt.invdtl_id is not null THEN tbl_invdtl.inv_id ELSE tbl_receipt.inv_id END)'))
            ->leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')
            ->leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')
            ->leftJoin('tbl_partner_prog', 'tbl_partner_prog.id', '=', 'tbl_invb2b.partnerprog_id')
            ->leftJoin('tbl_referral', 'tbl_referral.id', '=', 'tbl_invb2b.ref_id')
            ->select(DB::raw('COUNT(tbl_receipt.id) as count_receipt'), DB::raw('CAST(SUM(receipt_amount_idr) as integer) as total'))
            ->whereYear('tbl_receipt.created_at', '=', $year)
            ->whereMonth('tbl_receipt.created_at', '=', $month)
            ->where(
                DB::raw('(CASE
                            WHEN tbl_receipt.invdtl_id is not null THEN 
                            (CASE
                                WHEN tbl_invdtl.invb2b_id is not null THEN  
                                    (CASE 
                                        WHEN tbl_invb2b.schprog_id > 0 THEN tbl_sch_prog.status
                                        WHEN tbl_invb2b.partnerprog_id > 0 THEN tbl_partner_prog.status
                                        WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.referral_type
                                    END)
                                WHEN tbl_invdtl.inv_id is not null THEN tbl_client_prog.status
                            END)
                            WHEN tbl_receipt.inv_id is not null THEN tbl_client_prog.status
                            WHEN tbl_invb2b.schprog_id is not null THEN tbl_sch_prog.status
                            WHEN tbl_invb2b.partnerprog_id is not null THEN tbl_partner_prog.status
                            WHEN tbl_invb2b.ref_id > 0 THEN tbl_referral.referral_type
                    END)'),
                DB::raw('(CASE
                                WHEN tbl_invb2b.ref_id > 0 THEN "Out"
                                ELSE 1
                            END)')
            )
            ->whereNull('tbl_inv.bundling_id')
            ->groupBy(DB::raw('(CASE
                                    WHEN tbl_receipt.invb2b_id is not null THEN tbl_invb2b.invb2b_id
                                    WHEN tbl_receipt.inv_id is not null THEN tbl_inv.inv_id
                                END)'))
            ->get();
    }

    public function getDatatables($model)
    {
        return Datatables::eloquent($model)->make(true);
    }

    # signature
    public function getReceiptsNeedToBeSigned($asDatatables = false)
    {
        $response = Receipt::
            leftJoin('tbl_inv', 'tbl_inv.inv_id', '=', 'tbl_receipt.inv_id')->
            leftJoin('tbl_client_prog', 'tbl_client_prog.clientprog_id', '=', 'tbl_inv.clientprog_id')->
            leftJoin('program', 'program.prog_id', '=', 'tbl_client_prog.prog_id')->
            leftJoin('client', 'client.id', '=', 'tbl_client_prog.client_id')->
            leftJoin('tbl_receipt_attachment', 'tbl_receipt_attachment.receipt_id', '=', 'tbl_receipt.receipt_id')->
            where('receipt_status', 1)->
            whereNotNull('tbl_receipt.inv_id')->
            whereNotNull('tbl_receipt_attachment.receipt_id')->
            where('tbl_receipt_attachment.sign_status', 'not yet')->
            select([
                'client.full_name as fullname',
                'tbl_client_prog.clientprog_id',
                'tbl_receipt.id',
                'tbl_receipt.receipt_id',
                'program.program_name',
                'tbl_inv.inv_id',
                'tbl_inv.currency',
                'tbl_receipt.receipt_method as payment_method',
                'tbl_inv.created_at',
                'tbl_receipt.receipt_date as due_date',
                'tbl_receipt.receipt_amount_idr as total_price_idr',
                'tbl_receipt.receipt_amount as total_price',
                DB::raw('DATEDIFF(inv_duedate, now()) as date_difference')
            ])->
            orderBy('tbl_receipt.created_at', 'DESC');

        return $asDatatables === true ? $response : $response->get();
    }

    # CRM
    public function getAllReceiptFromCRM()
    {
        return V1Receipt::all();
    }

    public function getReceiptDifferences()
    {
        $receipt_v2 = Receipt::pluck('receipt_id')->toArray();

        return V1Receipt::whereNotIn('receipt_id', $receipt_v2)->get();
    }

    public function getReceiptRefFromCRM()
    {
        return V1Receipt::where('receipt_id', 'like', '%REF%')->get();
    }
}

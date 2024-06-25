<?php

namespace App\Repositories;

use App\Interfaces\InvoiceProgramRepositoryInterface;
use App\Models\Bundling;
use App\Models\ClientProgram;
use App\Models\InvoiceAttachment;
use App\Models\InvoiceProgram;
use App\Models\v1\Invoice as CRMInvoice;
use App\Models\ViewClientProgram;
use DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceProgramRepository implements InvoiceProgramRepositoryInterface
{
    public function getAllInvoiceProgramDataTables($status)
    {
        switch ($status) {

            case "needed":

                $regular = ClientProgram::
                        leftJoin('tbl_lead as cpl', 'cpl.lead_id', '=', 'tbl_client_prog.lead_id')->
                        leftJoin('tbl_eduf_lead as edl', 'edl.id', '=', 'tbl_client_prog.eduf_lead_id')->
                        leftJoin('tbl_client_event as ce', 'ce.clientevent_id', '=', 'tbl_client_prog.clientevent_id')->
                        leftJoin('tbl_events as e', 'e.event_id', '=', 'ce.event_id')->
                        leftJoin('tbl_corp as corp', 'corp.corp_id', '=', 'tbl_client_prog.partner_id')->
                        
                        when($status == "needed", function ($q) {
                        $q->where(function ($q2) use($q) {
                            # select all client program with relation bundling
                            # where status already success which mean they(client) already paid the program
                            $q2->whereHas('bundlingDetail', function ($q3) {
                                // $q3->whereHas('bundling', function ($q4){
                                //     $q4->doesntHave('invoice_b2c');
                                // });

                            # select all client program
                            # where status already success which mean they(client) already paid the program
                            })->orWhereDoesntHave('bundlingDetail', function ($q5) use ($q) {
                                $q->doesntHave('invoice');
                            });
                        });
                    })
                    ->where('tbl_client_prog.status', 1)->
                    select([
                        'tbl_client_prog.clientprog_id',
                        'tbl_client_prog.success_date',
                        'tbl_client_prog.client_id',
                        'tbl_client_prog.prog_id',
                        'tbl_client_prog.empl_id',
                        'tbl_client_prog.lead_id',
                    ]);
                   
                $query = $regular;  
                
                $fColumns = [
                    'relation' => ['client', 'viewProgram', 'internalPic'],
                    'columns' => ['fullname', 'program_name', 'pic_name'],
                    'realColumns' => [DB::raw('CONCAT(first_name COLLATE utf8mb4_unicode_ci, " ", COALESCE(last_name COLLATE utf8mb4_unicode_ci, ""))'), 'program_name', DB::raw('CONCAT(first_name COLLATE utf8mb4_unicode_ci, " ", COALESCE(last_name COLLATE utf8mb4_unicode_ci, ""))')]
                ];

                $datatable = DataTables::eloquent($query)->
                    addColumn('is_bundle', function ($query) {
                        return $query->bundlingDetail()->count();
                    })->
                    addColumn('bundling_id', function ($query) {
                        return $query->bundlingDetail()->count() > 0 ? $query->bundlingDetail->bundling_id : null;
                    })->            
                    addColumn('count_invoice', function ($query) {
                        return $query->bundlingDetail()->count() > 0 ? $query->bundlingDetail->bundling->invoice_b2c()->count() : 0;
                    })->
                    addColumn('fullname', function ($query) {
                        return $query->client->full_name;
                    })->
                    addColumn('program_name', function ($query) {
                        return $query->program->program_name;
                    })->
                    addColumn('pic_name', function ($query) {
                        return $query->internalPic->full_name;
                    })->
                    addColumn('conversion_lead', function (ClientProgram $clientProgram) {

                        $main_lead = $clientProgram->lead->main_lead;
                        $sub_lead = $clientProgram->lead->sub_lead;
                        switch ($main_lead) {
        
                            case "KOL":
                                $conv_lead = "KOL - {$sub_lead}";
                                break;
        
                            case "External Edufair":
                                $conv_lead = null;
                                if($clientProgram->eduf_lead_id == NULL){
                                    return $conv_lead = $clientProgram->lead->main_lead;
                                }
                
                                if ($clientProgram->external_edufair->title != NULL)
                                    $conv_lead = "External Edufair - " . $clientProgram->external_edufair->title;
                                else
                                    $conv_lead = "External Edufair - " . $clientProgram->external_edufair->organizerName;
                                break;
            
                  
        
                            case "All-In Event":
                                $event_title = $clientProgram->clientEvent->event->title;
                                $conv_lead = "EduALL Event - {$event_title}";
                                break;
        
                            case "All-In Partners":
                                $partner_name = $clientProgram->partner->corp_name;
                                $conv_lead = "EduALL Partners - {$partner_name}";
                                break;
        
                            default:
                                $conv_lead = $main_lead;
        
                        }
        
                        return $conv_lead;
                    });

                    foreach ($fColumns['columns'] as $key => $column) {
                        $datatable->filterColumn($column, function ($query, $keyword) use($fColumns, $key) {
                            $query->whereRelation($fColumns['relation'][$key], $fColumns['realColumns'][$key], 'like', "%{$keyword}%");
                        });
                    }
                    
                    $datatable->filterColumn('conversion_lead', function ($query, $keyword) {
                        $sql = "(CASE 
                                    WHEN cpl.main_lead COLLATE utf8mb4_unicode_ci = 'KOL' THEN CONCAT('KOL - ', cpl.sub_lead COLLATE utf8mb4_unicode_ci)
                                    WHEN cpl.main_lead COLLATE utf8mb4_unicode_ci = 'External Edufair' THEN CONCAT('External Edufair - ', edl.title COLLATE utf8mb4_unicode_ci)
                                    WHEN cpl.main_lead COLLATE utf8mb4_unicode_ci = 'EduALL Event' THEN CONCAT('All-In Event - ', e.event_title COLLATE utf8mb4_unicode_ci)
                                    WHEN cpl.main_lead COLLATE utf8mb4_unicode_ci = 'EduALL Partners' THEN CONCAT('All-In Partner - ', corp.corp_name COLLATE utf8mb4_unicode_ci)
                                    ELSE cpl.main_lead COLLATE utf8mb4_unicode_ci
                                END) like ?";
                        $query->whereRaw($sql, ["%{$keyword}%"]);
                    });

                    return $datatable->make(true);

                break;

            case "list":
                $query = ClientProgram::leftJoin('tbl_inv', 'tbl_inv.clientprog_id', '=', 'tbl_client_prog.clientprog_id')
                    ->select([
                        'tbl_client_prog.clientprog_id',
                        'inv_id',
                        'inv_paymentmethod',
                        'tbl_inv.created_at',
                        'inv_duedate',
                        'inv_totalprice_idr',
                        'tbl_client_prog.status',
                        'tbl_client_prog.prog_id',
                        'tbl_client_prog.client_id'
                    ])->orderBy('tbl_inv.updated_at', 'desc')->groupBy('tbl_inv.inv_id');

                $fColumns = [
                    'relation' => ['viewProgram', 'client'],
                    'columns' => ['program_name', 'fullname'],
                    'realColumns' => ['program_name', DB::raw('CONCAT(first_name COLLATE utf8mb4_unicode_ci, " ", COALESCE(last_name COLLATE utf8mb4_unicode_ci, ""))')]
                ];

                $datatable = DataTables::eloquent($query)->
                    addColumn('is_bundle', function ($query) {
                        return $query->bundlingDetail()->count();
                    })->
                    addColumn('bundling_id', function ($query) {
                        return $query->bundlingDetail()->count() > 0 ? $query->bundlingDetail->bundling_id : null;
                    })->
                    addColumn('program_name', function ($query) {
                        return $query->program->program_name;
                    })->
                    addColumn('fullname', function ($query) {
                        return $query->client->full_name;
                    });
  
                foreach ($fColumns['columns'] as $key => $column) {
                    $datatable->filterColumn($column, function ($query, $keyword) use($fColumns, $key) {
                        $query->whereRelation($fColumns['relation'][$key], $fColumns['realColumns'][$key], 'like', "%{$keyword}%");
                    });
                }
                
                return $datatable->make(true);
                break;

            case "reminder":

                $query = ClientProgram::with('client.parents')->leftJoin('tbl_inv', 'tbl_inv.clientprog_id', '=', 'tbl_client_prog.clientprog_id')->leftJoin('tbl_invdtl', 'tbl_invdtl.inv_id', '=', 'tbl_inv.inv_id')->leftJoin('tbl_client as child', 'child.id', '=', 'tbl_client_prog.client_id')->leftJoin('tbl_client_relation', 'tbl_client_relation.child_id', '=', 'child.id')->leftJoin('tbl_client as parent', 'parent.id', '=', 'tbl_client_relation.parent_id')->leftJoin('tbl_receipt as receipt', 'receipt.inv_id', '=', 'tbl_inv.inv_id')->select([
                    'tbl_inv.clientprog_id',
                    DB::raw('CONCAT(child.first_name, " ", COALESCE(child.last_name, "")) as fullname'),
                    DB::raw('CONCAT(parent.first_name, " ", COALESCE(parent.last_name, "")) as parent_fullname'),
                    'parent.phone as parent_phone',
                    'parent.id as parent_id',
                    'child.id as client_id',
                    'child.phone as child_phone',
                    'tbl_inv.inv_id',
                    'tbl_client_prog.prog_id',
                    DB::raw('
                            (CASE
                                WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_paymentmethod
                                WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_installment
                            END) as payment_method
                        '),
                    // 'tbl_inv.inv_paymentmethod',
                    DB::raw('
                            (CASE
                                WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.created_at
                                WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.created_at
                            END) as show_created_at
                        '),
                    // 'tbl_inv.created_at',
                    DB::raw('
                            (CASE
                                WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_duedate
                                WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_duedate
                            END) as due_date
                        '),
                    // 'tbl_inv.inv_duedate',
                    DB::raw('
                            (CASE
                                WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_totalprice_idr
                                WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_amountidr
                            END) as total_price_idr
                        '),
                    // 'tbl_inv.inv_totalprice_idr',
                    DB::raw('
                            (CASE
                                WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN DATEDIFF(tbl_inv.inv_duedate, now())
                                WHEN tbl_inv.inv_paymentmethod = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                            END) as date_difference
                        '),
                    // DB::raw('DATEDIFF(tbl_inv.inv_duedate, now()) as date_difference')
                ])
                    ->whereNull('receipt.inv_id')
                    ->where(DB::raw('
                        (CASE
                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN DATEDIFF(tbl_inv.inv_duedate, now())
                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                        END)
                    '), '<=', 7)
                    ->orderBy(DB::raw('
                        (CASE
                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN DATEDIFF(tbl_inv.inv_duedate, now())
                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                        END)
                    '), 'desc')
                    ->groupBy('tbl_inv.inv_id');

                return DataTables::eloquent($query)->
                addColumn('program_name', function ($query) {
                    return $query->program->program_name;
                })->filterColumn('payment_method', function ($query, $keyword) {
                    $sql = '(CASE
                                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_paymentmethod
                                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_installment
                                        END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })->filterColumn('show_created_at', function ($query, $keyword) {
                    $sql = '(CASE
                                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.created_at
                                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.created_at
                                        END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })->filterColumn('due_date', function ($query, $keyword) {
                    $sql = '(CASE
                                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_duedate
                                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_duedate
                                        END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })->filterColumn('total_price_idr', function ($query, $keyword) {
                    $sql = '(CASE
                                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_totalprice_idr
                                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_amountidr
                                        END) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })->filterColumn('program_name', function ($query, $keyword) {
                    $query->whereRelation('viewProgram', 'program_name', 'like', "%{$keyword}%");
                })->filterColumn('fullname', function ($query, $keyword) {
                    $sql = 'CONCAT(child.first_name, " ", COALESCE(child.last_name, "")) like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })->toJson();
                return DataTables::eloquent($query)->make(true);
                break;
        }


    }

    public function getAllDueDateInvoiceProgram(int $days)
    {
        return ViewClientProgram::
            leftJoin('tbl_inv', 'tbl_inv.clientprog_id', '=', 'clientprogram.clientprog_id')->
            leftJoin('tbl_invdtl', 'tbl_invdtl.inv_id', '=', 'tbl_inv.inv_id')->
            leftJoin('tbl_client as child', 'child.id', '=', 'clientprogram.client_id')->
            leftJoin('tbl_client_relation', 'tbl_client_relation.child_id', '=', 'child.id')->
            leftJoin('tbl_client as parent', 'parent.id', '=', 'tbl_client_relation.parent_id')->
            leftJoin('tbl_receipt as r', 'r.inv_id', '=', 'tbl_inv.inv_id')->
            leftJoin('tbl_receipt as dr', 'dr.invdtl_id', '=', 'tbl_invdtl.invdtl_id')->
            select([
                'tbl_inv.clientprog_id',
                'clientprogram.fullname',
                'clientprogram.parent_fullname',
                'clientprogram.parent_phone',
                'clientprogram.parent_mail',
                'clientprogram.status',
                'program_name',
                'tbl_inv.currency',
                'tbl_inv.inv_paymentmethod as master_paymentmethod',
                'tbl_inv.inv_id',
                DB::raw('
                    (CASE
                        WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.id
                        WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_id
                    END) as identifier
                '),
                DB::raw('
                    (CASE
                        WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_paymentmethod
                        WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_installment
                    END) as inv_paymentmethod
                '),
                // 'tbl_inv.inv_paymentmethod',
                DB::raw('
                    (CASE
                        WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.created_at
                        WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.created_at
                    END) as show_created_at
                '),
                // 'tbl_inv.created_at',
                DB::raw('
                    (CASE
                        WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_duedate
                        WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_duedate
                    END) as inv_duedate
                '),
                // 'tbl_inv.inv_duedate',
                DB::raw('
                    (CASE
                        WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_totalprice_idr
                        WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_amountidr
                    END) as inv_totalprice_idr
                '),
                DB::raw('
                    (CASE
                        WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_totalprice
                        WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_amount
                    END) as inv_totalprice
                '),
                // 'tbl_inv.inv_totalprice_idr',
                'pic_mail',
                DB::raw('
                    (CASE
                        WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN DATEDIFF(tbl_inv.inv_duedate, now())
                        WHEN tbl_inv.inv_paymentmethod = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                    END) as date_difference
                '),
                // DB::raw('DATEDIFF(tbl_inv.inv_duedate, now()) as date_difference')
            ])->
            // whereNull('tbl_receipt.inv_id')
            whereRaw('
                (CASE
                    WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.reminded = 0 OR tbl_inv.reminded IS NULL
                    WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.reminded = 0 OR tbl_invdtl.reminded IS NULL
                END)
            ')
            // ->where(DB::raw('DATEDIFF(inv_duedate, now())'), '=', $days)
            ->where(DB::raw('
                (CASE
                    WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN DATEDIFF(tbl_inv.inv_duedate, now())
                    WHEN tbl_inv.inv_paymentmethod = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                END)
            '), '=', $days)->
            whereNull(DB::raw('
                (CASE
                    WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN r.inv_id
                    WHEN tbl_inv.inv_paymentmethod = "Installment" THEN dr.invdtl_id
                END)
            '))->
            # add new condition
            # not included refunded invoice
            whereIn('tbl_inv.inv_status', [0,1])->
            # not included status program failed
            where('clientprogram.status', 1)->
            // where('tbl_inv.inv_status', 1)->
            orderBy('date_difference', 'asc')->
            groupBy('tbl_inv.inv_id')->
            get();
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
        $invoiceWasChanged = false;

        /* remove unused field */
        unset($invoiceDetails['is_session']);
        unset($invoiceDetails['invoice_date']);
        

        /* disable updating update_at temporarily */
        $invoiceDetails['timestamps'] = false;
        
        /* query */
        $invoiceProgram = InvoiceProgram::where('inv_id', $invoiceId)->first();
        $invoiceProgram->fill($invoiceDetails);
        $invoiceProgram->save($invoiceDetails);

        /* if the invoice program was changed, then remove the attachments in order to able to do request sign */
        if ($invoiceProgram->wasChanged()) {
            
            Log::info('Deleted invoice attachment of invoice number : '.$invoiceId.' because there was a changes in the invoice such as :'. json_encode($invoiceDetails));
            $invoiceWasChanged = true;
        }

        return [
            'invoiceProgram' => $invoiceProgram,
            'invoiceWasChanged' => $invoiceWasChanged,
        ];
            
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

        $queryInv->whereNull('bundling_id');

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

        return $queryInv->orderBy('tbl_inv.inv_id', 'ASC')->withCount('invoiceDetail')->get();
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
                DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_inv.inv_id, '/', 1), '/', -1) as 'inv_id_num'"),
                DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_inv.inv_id, '/', 4), '/', -1) as 'inv_id_month'"),
                DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_inv.inv_id, '/', 5), '/', -1) as 'inv_id_year'"),
                'tbl_inv.clientprog_id',
                'tbl_inv.inv_duedate as invoice_duedate',
                'tbl_inv.currency',
                'tbl_invdtl.invdtl_duedate as installment_duedate',
                DB::raw('(CASE
                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN 
                                tbl_inv.inv_totalprice_idr 
                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN 
                                tbl_invdtl.invdtl_amountidr
                            ELSE null
                        END) as total_price_inv_idr'),
                DB::raw('(CASE
                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN 
                                tbl_inv.inv_totalprice
                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN 
                                tbl_invdtl.invdtl_amount
                            ELSE null
                        END) as total_price_inv_other'),
                'tbl_receipt.receipt_id',
                'tbl_receipt.receipt_amount_idr',
                'tbl_receipt.created_at as paid_date',
                'tbl_invdtl.invdtl_installment',
                'tbl_invdtl.invdtl_id',
            )->where('tbl_receipt.receipt_id', '=', NULL)
            ->whereRelation('clientprog', 'status', 1);

        if (isset($start_date) && isset($end_date)) {
            return $invoiceB2c->whereBetween($whereBy, [$start_date, $end_date])
                ->orderBy('inv_id_num', 'asc')
                ->orderBy('inv_id_month', 'asc')
                ->orderBy('inv_id_year', 'asc')
                ->get();
        } else if (isset($start_date) && !isset($end_date)) {
            return $invoiceB2c->whereDate($whereBy, '>=', $start_date)
                ->orderBy('inv_id_num', 'asc')
                ->orderBy('inv_id_month', 'asc')
                ->orderBy('inv_id_year', 'asc')
                ->get();
        } else if (!isset($start_date) && isset($end_date)) {
            return $invoiceB2c->whereDate($whereBy, '<=', $end_date)
                ->orderBy('inv_id_num', 'asc')
                ->orderBy('inv_id_month', 'asc')
                ->orderBy('inv_id_year', 'asc')
                ->get();
        } else {
            return $invoiceB2c->whereBetween($whereBy, [$firstDay, $lastDay])
                ->orderBy('inv_id_num', 'asc')
                ->orderBy('inv_id_month', 'asc')
                ->orderBy('inv_id_year', 'asc')
                ->get();
        }
    }

    public function getTotalInvoiceNeeded($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        return ViewClientProgram::doesntHave('invoice')
            ->leftJoin('program', 'program.prog_id', '=', 'clientprogram.prog_id')
            ->select(
                'fullname as client_name',
                'program.program_name',
                'pic_name',
                'success_date',
                'clientprog_id as client_prog_id',
                DB::raw("'client_prog' as type"),

            )
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
            ->whereNull('tbl_inv.bundling_id')
            ->get();
    }

    public function getTotalRefundRequest($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        return ClientProgram::leftJoin('tbl_inv', 'tbl_inv.clientprog_id', '=', 'tbl_client_prog.clientprog_id')
            ->select(DB::raw("count('tbl_client_prog.clientprog_id') as count_refund_request"))
            ->where('tbl_client_prog.status', 3)
            ->where('tbl_inv.inv_status', 1)
            ->whereYear('tbl_client_prog.refund_date', '=', $year)
            ->whereMonth('tbl_client_prog.refund_date', '=', $month)
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
            ->leftJoin('tbl_client as child', 'child.id', '=', 'tbl_client_prog.client_id')
            ->leftJoin('tbl_client_relation', 'tbl_client_relation.child_id', '=', 'child.id')
            ->leftJoin('tbl_client as parent', 'parent.id', '=', 'tbl_client_relation.parent_id');

        switch ($type) {
            case 'paid':
                $queryInv->select([
                    'tbl_inv.inv_id as invoice_id',
                    DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_inv.inv_id, '/', 1), '/', -1) as 'inv_id_num'"),
                    DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_inv.inv_id, '/', 4), '/', -1) as 'inv_id_month'"),
                    DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_inv.inv_id, '/', 5), '/', -1) as 'inv_id_year'"),
                    'tbl_inv.clientprog_id',
                    DB::raw('CONCAT(child.first_name, " ", COALESCE(child.last_name, "")) as full_name'),
                    'parent.phone as parent_phone',
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
                    DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_inv.inv_id, '/', 1), '/', -1) as 'inv_id_num'"),
                    DB::raw("ABS(SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_inv.inv_id, '/', 4), '/', -1)) as 'inv_id_month'"),
                    DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(tbl_inv.inv_id, '/', 5), '/', -1) as 'inv_id_year'"),
                    'tbl_inv.clientprog_id',
                    'tbl_inv.clientprog_id as client_prog_id',
                    'child.id as client_id',
                    DB::raw('CONCAT(child.first_name, " ", COALESCE(child.last_name, "")) as full_name'),
                    'child.phone as child_phone',
                    'parent.phone as parent_phone',
                    DB::raw('CONCAT(parent.first_name, " ", COALESCE(parent.last_name, "")) as parent_name'),
                    'parent.id as parent_id',
                    'program.program_name',
                    // DB::raw('CONCAT(prog_program, " - ", COALESCE(tbl_main_prog.prog_name, ""), COALESCE(CONCAT(" / ", tbl_sub_prog.sub_prog_name), "")) as program_name'),
                    'tbl_invdtl.invdtl_installment as installment_name',
                    DB::raw("'client_prog' as typeprog"),
                    DB::raw("'B2C' as type"),
                    DB::raw('(CASE
                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN 
                                tbl_inv.inv_totalprice_idr 
                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN 
                                tbl_invdtl.invdtl_amountidr
                            ELSE null
                        END) as total'),

                    DB::raw('(CASE 
                                WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN 
                                    tbl_inv.inv_duedate 
                                WHEN tbl_inv.inv_paymentmethod = "Installment" THEN 
                                    tbl_invdtl.invdtl_duedate
                            END) as invoice_duedate')
                ])
                ->whereNull('tbl_receipt.id');

                if (isset($monthYear)) {
                    $queryInv->whereYear($whereBy, '=', $year)
                        ->whereMonth($whereBy, '=', $month);
                } else {
                    $queryInv->whereBetween($whereBy, [$start_date, $end_date]);
                }
                break;
        }

        $queryInv
            ->whereRelation('clientprog', 'status', 1)
            ->whereNull('tbl_inv.bundling_id');
        // ->groupBy('tbl_inv.inv_id');

        return $queryInv->orderBy('inv_id_year', 'asc')->orderBy('inv_id_month', 'asc')->orderBy('inv_id_num', 'asc')->groupBy('invoice_id')->get();
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
            ->whereNull('tbl_inv.bundling_id')
            ->groupBy(DB::raw('MONTH(tbl_receipt.created_at)'))
            ->get();
    }

    public function getDatatables($model)
    {
        return Datatables::eloquent($model)->make(true);
    }

    # signature
    public function getInvoicesNeedToBeSigned($asDatatables = false)
    {

        $response = ViewClientProgram::
            leftJoin('tbl_inv', 'tbl_inv.clientprog_id', '=', 'clientprogram.clientprog_id')->
            leftJoin('tbl_invdtl', 'tbl_invdtl.inv_id', '=', 'tbl_inv.inv_id')->
            leftJoin('tbl_inv_attachment', 'tbl_inv_attachment.inv_id', '=', 'tbl_inv.inv_id')->
            leftJoin('tbl_client as child', 'child.id', '=', 'clientprogram.client_id')->
            leftJoin('tbl_client_relation', 'tbl_client_relation.child_id', '=', 'child.id')->
            leftJoin('tbl_client as parent', 'parent.id', '=', 'tbl_client_relation.parent_id')->
            leftJoin('tbl_receipt as receipt', 'receipt.inv_id', '=', 'tbl_inv.inv_id')->
            select([
                'tbl_inv.clientprog_id',
                'clientprogram.fullname',
                'clientprogram.parent_fullname',
                'clientprogram.parent_phone',
                'parent.id as parent_id',
                'child.id as client_id',
                'child.phone as child_phone',
                'program_name',
                'tbl_inv.inv_id',
                'tbl_inv.inv_category as currency_category',
                'tbl_inv.currency',
                DB::raw('
                        (CASE
                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_paymentmethod
                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_installment
                        END) as payment_method
                    '),
                DB::raw('
                        (CASE
                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.created_at
                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.created_at
                        END) as show_created_at
                    '),
                DB::raw('
                        (CASE
                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_duedate
                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_duedate
                        END) as due_date
                    '),
                DB::raw('
                        (CASE
                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_totalprice
                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_amount
                        END) as total_price
                    '),
                DB::raw('
                        (CASE
                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_totalprice_idr
                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_amountidr
                        END) as total_price_idr
                    '),
                DB::raw('
                        (CASE
                            WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN DATEDIFF(tbl_inv.inv_duedate, now())
                            WHEN tbl_inv.inv_paymentmethod = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                        END) as date_difference
                    '),
            ])->
            whereNotNull('tbl_inv.inv_id')->
            whereNotNull('tbl_inv_attachment.inv_id')->
            where('tbl_inv_attachment.sign_status', 'not yet')->
            where(DB::raw('
                (CASE
                    WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN DATEDIFF(tbl_inv.inv_duedate, now())
                    WHEN tbl_inv.inv_paymentmethod = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                END)
            '), '<=', 7)->
            orderBy(DB::raw('
                (CASE
                    WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN DATEDIFF(tbl_inv.inv_duedate, now())
                    WHEN tbl_inv.inv_paymentmethod = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                END)
            '), 'desc');

        return $asDatatables === true ? $response : $response->get();
    }

    public function getInvoiceDifferences()
    {
        $invoice_v2 = InvoiceProgram::pluck('inv_id')->toArray();

        return CRMInvoice::whereNotIn('inv_id', $invoice_v2)->get();
    }


    ###############################################################
    ######################### BUNDLING ############################
    ###############################################################

    public function getInvoiceByBundlingId($bundlingId)
    {
        return InvoiceProgram::where('bundling_id', $bundlingId)->first();
    }

    public function deleteInvoiceByBundlingId($bundlingId)
    {
        return InvoiceProgram::where('bundling_id', $bundlingId)->delete();
    }

    public function getProgramBundle_InvoiceProgram($status)
    {
        switch ($status) {

            case "needed":
                $query = Bundling::whereDoesntHave('invoice_b2c');
                
                break;

            case "list":
                $query = Bundling::leftJoin('tbl_inv', 'tbl_inv.bundling_id', 'tbl_bundling.uuid')
                        ->select([
                            'tbl_bundling.uuid',
                            'tbl_inv.inv_id',
                            'tbl_inv.inv_paymentmethod',
                            'tbl_inv.created_at',
                            'tbl_inv.inv_duedate',
                            'tbl_inv.inv_totalprice_idr',
                        ])
                        ->where('tbl_inv.bundling_id', '!=', null);
                break;

            case "reminder":
                $query = Bundling::leftJoin('tbl_inv', 'tbl_inv.bundling_id', 'tbl_bundling.uuid')
                        ->leftJoin('tbl_invdtl', 'tbl_invdtl.inv_id', '=', 'tbl_inv.inv_id')
                        ->select([
                            'uuid',
                            'tbl_inv.inv_id',
                            DB::raw('
                                    (CASE
                                        WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_paymentmethod
                                        WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_installment
                                    END) as payment_method
                                '),
                            // 'tbl_inv.inv_paymentmethod',
                            DB::raw('
                                    (CASE
                                        WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.created_at
                                        WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.created_at
                                    END) as show_created_at
                                '),
                            // 'tbl_inv.created_at',
                            DB::raw('
                                    (CASE
                                        WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_duedate
                                        WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_duedate
                                    END) as due_date
                                '),
                            // 'tbl_inv.inv_duedate',
                            DB::raw('
                                    (CASE
                                        WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_totalprice_idr
                                        WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_amountidr
                                    END) as total_price_idr
                                '),
                            // 'tbl_inv.inv_totalprice_idr',
                            DB::raw('
                                    (CASE
                                        WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN DATEDIFF(tbl_inv.inv_duedate, now())
                                        WHEN tbl_inv.inv_paymentmethod = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                                    END) as date_difference
                                '),
                            // DB::raw('DATEDIFF(tbl_inv.inv_duedate, now()) as date_difference')
                        ])
                        ->where('tbl_inv.bundling_id', '!=', null)
                        ->whereDoesntHave('invoice_b2c.receipt')
                        // ->where(DB::raw('
                        //     (CASE
                        //         WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN DATEDIFF(tbl_inv.inv_duedate, now())
                        //         WHEN tbl_inv.inv_paymentmethod = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                        //     END)
                        // '), '<=', 7)
                        ->orderBy(DB::raw('
                            (CASE
                                WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN DATEDIFF(tbl_inv.inv_duedate, now())
                                WHEN tbl_inv.inv_paymentmethod = "Installment" THEN DATEDIFF(tbl_invdtl.invdtl_duedate, now())
                            END)
                        '), 'desc')
                        ->groupBy('tbl_inv.inv_id');

                     return DataTables::eloquent($query)->
                        addColumn('fullname', function (Bundling $bundle) {
                            return $bundle->details()->first()->client_program->client->full_name;
                        })->
                        addColumn('program_name', function (Bundling $bundle) {
                            return $bundle->details()->first()->client_program->program->program_name . ' (Bundle Package)';
                        })->
                        addColumn('parent_fullname', function (Bundling $bundle) {
                            return $bundle->details()->first()->client_program->client->parents()->first()->full_name;
                        })->
                        addColumn('parent_phone', function (Bundling $bundle) {
                            return $bundle->details()->first()->client_program->client->parents()->first()->phone;
                        })->
                        addColumn('parent_id', function (Bundling $bundle) {
                            return $bundle->details()->first()->client_program->client->parents()->first()->id;
                        })->
                        addColumn('client_id', function (Bundling $bundle) {
                            return $bundle->details()->first()->client_program->client->id;
                        })->
                        addColumn('client_phone', function (Bundling $bundle) {
                            return $bundle->details()->first()->client_program->client->phone;
                        })->
                        filterColumn('fullname', function ($query, $keyword) {
                            $query->whereRelation('first_detail.client_program.client', DB::raw('CONCAT(first_name COLLATE utf8mb4_unicode_ci, " ", COALESCE(last_name COLLATE utf8mb4_unicode_ci, ""))'), 'like', "%{$keyword}%");
                        })->
                        filterColumn('payment_method', function ($query, $keyword) {
                            $sql = '(CASE
                                                    WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_paymentmethod
                                                    WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_installment
                                                END) like ?';
                            $query->whereRaw($sql, ["%{$keyword}%"]);
                        })->filterColumn('show_created_at', function ($query, $keyword) {
                            $sql = '(CASE
                                                    WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.created_at
                                                    WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.created_at
                                                END) like ?';
                            $query->whereRaw($sql, ["%{$keyword}%"]);
                        })->filterColumn('due_date', function ($query, $keyword) {
                            $sql = '(CASE
                                                    WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_duedate
                                                    WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_duedate
                                                END) like ?';
                            $query->whereRaw($sql, ["%{$keyword}%"]);
                        })->filterColumn('total_price_idr', function ($query, $keyword) {
                            $sql = '(CASE
                                                    WHEN tbl_inv.inv_paymentmethod = "Full Payment" THEN tbl_inv.inv_totalprice_idr
                                                    WHEN tbl_inv.inv_paymentmethod = "Installment" THEN tbl_invdtl.invdtl_amountidr
                                                END) like ?';
                            $query->whereRaw($sql, ["%{$keyword}%"]);
                        })->toJson();
                break;

        }   
        
        return DataTables::eloquent($query)->
            addColumn('fullname', function ($query) {
                return $query->first_detail->client_program->client->full_name;
            })->
            addColumn('program_name', function (Bundling $bundle) {
                $program_names = [];
                foreach ($bundle->details as $detail) {
                    $program_names[] =  $detail->client_program->program->program_name;
                }
                return implode(', ', $program_names);
            })->
            filterColumn('fullname', function ($query, $keyword) {
                $query->whereRelation('first_detail.client_program.client', DB::raw('CONCAT(first_name COLLATE utf8mb4_unicode_ci, " ", COALESCE(last_name COLLATE utf8mb4_unicode_ci, ""))'), 'like', "%{$keyword}%");
            })->
            toJson();
    }
}

<?php

namespace App\Repositories;

use App\Interfaces\SalesTargetRepositoryInterface;
use App\Models\ClientProgram;
use App\Models\InvoiceProgram;
use App\Models\SalesTarget;
use App\Models\User;
use DataTables;
use Illuminate\Support\Facades\DB;

class SalesTargetRepository implements SalesTargetRepositoryInterface
{

    public function getMonthlySalesTarget($programId, $filter)
    {
        return SalesTarget::when($programId, function ($query) use ($programId) {
            $query->where('prog_id', $programId);
        })
        ->when($filter['qdate'], function ($query) use ($filter) {
            $query->whereMonth('month_year', date('m', strtotime($filter['qdate'])))
                ->whereYear('month_year', date('Y', strtotime($filter['qdate'])));
        })
        ->select([
            DB::raw("CONCAT(CAST(YEAR(month_year) AS CHAR(4)), '-', LPAD(CAST(MONTH(month_year) AS CHAR(2)), 2, '0')) as month_year"),
            DB::raw('SUM(total_participant) as total_participant'),
            DB::raw('SUM(total_target) as total_target'),
        ])
        ->groupBy(DB::raw("CONCAT(CAST(YEAR(month_year) AS CHAR(4)), '-', LPAD(CAST(MONTH(month_year) AS CHAR(2)), 2, '0'))"))
        ->first();
    }


    public function getMonthlySalesActual($programId, $filter)
    {
        $userId = null;
        if (isset($filter['quuid'])) {
            $user = User::where('id', $filter['quuid'])->first();
            $userId = $user->id;
        }

        $salesTarget = SalesTarget::when($programId, function ($query) use ($programId) {
            $query->where('tbl_sales_target.prog_id', $programId);
        })->when($filter['qdate'], function ($query) use ($filter) {
            $query->whereMonth('tbl_sales_target.month_year', date('m', strtotime($filter['qdate'])))->whereYear('tbl_sales_target.month_year', date('Y', strtotime($filter['qdate'])));
        })->groupBy(DB::raw('(CASE WHEN tbl_sales_target.prog_id is null THEN tbl_sales_target.main_prog_id ELSE tbl_sales_target.prog_id END)'))->get();


        $mapping = $salesTarget->map(function ($item) use ($filter, $userId) {

            $totalActualParticipant = ClientProgram::whereHas('program', function($q) use($item){
                if($item->prog_id == null){
                    $q->where('main_prog_id', $item->main_prog_id);
                }else{
                    $q->where('prog_id', $item->prog_id);
                }
            })->when($userId, function($query) use($userId){
                $query->where('empl_id', $userId);
            })
            ->select(DB::raw('count(*) as count_participant'))
            ->where('status', 1)
            ->whereMonth('success_date', date('m', strtotime($filter['qdate'])))
            ->whereYear('success_date', date('Y', strtotime($filter['qdate'])))
            ->first();
    
            $totalActualAmount = InvoiceProgram::leftJoin('tbl_client_prog', 'tbl_inv.clientprog_id', '=', 'tbl_client_prog.clientprog_id')
                ->whereHas('clientprog', function($q) use($item, $userId){
                    $q->whereHas('program', function($q2) use($item){
                        if($item->prog_id == null){
                            $q2->where('main_prog_id', $item->main_prog_id);
                        }else{
                            $q2->where('prog_id', $item->prog_id);
                        }
                    })->when($userId, function($query) use($userId){
                        $query->where('empl_id', $userId);
                    });
                })->select(DB::raw('SUM(tbl_inv.inv_totalprice_idr) as total_actual_amount'))
                ->where('tbl_client_prog.status', 1)
                ->whereMonth('tbl_client_prog.success_date', date('m', strtotime($filter['qdate'])))
                ->whereYear('tbl_client_prog.success_date', date('Y', strtotime($filter['qdate'])))
                ->first();
    
            return [
                'total_participant' => $totalActualParticipant->count_participant,
                'total_target' => $totalActualAmount->total_actual_amount
            ];
        });

        return [
            'total_participant' => $mapping->sum('total_participant'),
            'total_target' => $mapping->sum('total_target'),
        ];
    }

    public function getSalesDetail($programId, $filter)
    {
        $userId = null;
        if (isset($filter['quuid'])) {
            $user = User::where('id', $filter['quuid'])->first();
            $userId = $user->id;
        }

        $usingProgramId = $programId ? true : false;
        $usingFilterDate = $filter['qdate'] ? true : false;
        $usingUuid = $userId ? true : false;

        
        $salesTarget = SalesTarget::when($usingProgramId, function ($query) use ($programId) {
            $query->where('tbl_sales_target.prog_id', $programId);
        })->when($usingFilterDate, function ($query) use ($filter) {
            $query->whereMonth('tbl_sales_target.month_year', date('m', strtotime($filter['qdate'])))->whereYear('tbl_sales_target.month_year', date('Y', strtotime($filter['qdate'])));
        })->groupBy(DB::raw('(CASE WHEN tbl_sales_target.prog_id is null THEN tbl_sales_target.main_prog_id ELSE tbl_sales_target.prog_id END)'))->get();

        $mapping = $salesTarget->map(function ($item) use ($filter, $userId) {

            $totalActualParticipant = ClientProgram::whereHas('program', function($q) use($item){
                if($item->prog_id == null){
                    $q->where('main_prog_id', $item->main_prog_id);
                }else{
                    $q->where('prog_id', $item->prog_id);
                }
            })->when($userId, function($query) use($userId){
                $query->where('empl_id', $userId);
            })
            ->select(DB::raw('count(*) as count_participant'))
            ->where('status', 1)
            ->whereMonth('success_date', date('m', strtotime($filter['qdate'])))
            ->whereYear('success_date', date('Y', strtotime($filter['qdate'])))
            ->first();
    
            $totalActualAmount = InvoiceProgram::leftJoin('tbl_client_prog', 'tbl_inv.clientprog_id', '=', 'tbl_client_prog.clientprog_id')
                ->whereHas('clientprog', function($q) use($item, $userId){
                    $q->whereHas('program', function($q2) use($item){
                        if($item->prog_id == null){
                            $q2->where('main_prog_id', $item->main_prog_id);
                        }else{
                            $q2->where('prog_id', $item->prog_id);
                        }
                    })->when($userId, function($query) use($userId){
                        $query->where('empl_id', $userId);
                    });
                })->select(DB::raw('SUM(tbl_inv.inv_totalprice_idr) as total_actual_amount'))
                ->where('tbl_client_prog.status', 1)
                ->whereMonth('tbl_client_prog.success_date', date('m', strtotime($filter['qdate'])))
                ->whereYear('tbl_client_prog.success_date', date('Y', strtotime($filter['qdate'])))
                ->first();
    
            return [
                'main_prog_id' => $item->main_prog_id,
                'prog_id' => $item->prog_id,
                'program_name_sales' => $item->prog_id == null ? $item->main_program->prog_name : $item->program->program_name,
                'total_target_participant' => $item->total_participant,
                'total_target' => $item->total_target,
                'total_actual_participant' => $totalActualParticipant->count_participant,
                'total_actual_amount' => $totalActualAmount->total_actual_amount
            ];
        });
    

        return $mapping;
    }
   
    public function rnGetSalesDetailFromClientProgram(array $date_details, array $additional_filter = [])
    {
        
        # array of additional filter is filled with [main_prog_id, prog_id, pic]
        $main_prog_id = $additional_filter['main_prog_id'];
        $prog_id = $additional_filter['prog_id'];
        $pic = $additional_filter['pic'];

        $userId = null;

        $usingFilterDate = count($date_details) > 0 ? true : false;

        // 1. condition for building query
        $date_condition = 'AND q_cp.success_date between \'' .$date_details['start']. '\' AND \'' . $date_details['end'] . '\'';
        $pic_condition = $pic ? 'AND empl_id = \''.$pic.'\'' : null;
        
        // 2. building query for select
        $query_total_actual_participant = '(SELECT COUNT(*) FROM tbl_client_prog as q_cp WHERE q_cp.prog_id = cp_p.prog_id AND q_cp.status = 1 '.$date_condition.' '.$pic_condition.' )';

        $query_total_actual_amount = '(SELECT SUM(q_i.inv_totalprice_idr) FROM tbl_client_prog as q_cp LEFT JOIN tbl_inv q_i ON q_i.clientprog_id = q_cp.clientprog_id WHERE q_cp.prog_id = cp_p.prog_id AND q_cp.status = 1 '.$date_condition.' '.$pic_condition.')';


        return ClientProgram::query()->
            leftJoin('tbl_inv', 'tbl_inv.clientprog_id', '=', 'tbl_client_prog.clientprog_id')->
            leftJoin('tbl_prog as cp_p', 'cp_p.prog_id', '=', 'tbl_client_prog.prog_id')->
            leftJoin('tbl_main_prog as cp_mp', 'cp_mp.id', '=', 'cp_p.main_prog_id')->
            when($usingFilterDate, function ($query) use ($date_details) {
                $query->where(function ($q) use ($date_details) {
                    $q->whereBetween('tbl_client_prog.success_date', [$date_details['start'], $date_details['end']]);
                });
            })->
            when($main_prog_id, function ($query) use ($main_prog_id) {
                $query->where('cp_mp.id', $main_prog_id);
            })->
            when($prog_id, function ($query) use ($prog_id) {
                $query->where('cp_p.prog_id', $prog_id);
            })-> 
            when($pic, function ($query) use ($pic) {
                # check the client pic
                // $query->where('empl_id', $pic);
                $query->where(function ($sq_1) use ($pic) {
                    $sq_1->whereHas('client', function ($sq_2) use ($pic) {
                        $sq_2->whereHas('handledBy', function ($sq_3) use ($pic) {
                            $sq_3->where('users.id', $pic);
                        });
                    })->
                    # and check the pic client program
                    orWhere('empl_id', $pic);
                });
            })->

            

            select([
                'cp_p.prog_id',
                DB::raw('CONCAT(cp_mp.prog_name, ": ", cp_p.prog_program) as program_name_sales'),
                DB::raw("{$query_total_actual_participant} as total_actual_participant"),
                DB::raw("{$query_total_actual_amount} as total_actual_amount"),
            ])->groupBy('cp_p.prog_id', DB::raw('CONCAT(cp_mp.prog_name, ": ", cp_p.prog_program)'))->get();
    }

    public function getAllSalesTargetDataTables()
    {
        return datatables::eloquent(
            SalesTarget::leftJoin('program', 'program.prog_id', '=', 'tbl_sales_target.prog_id')->
                    leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_sales_target.main_prog_id')->
                select(
                'tbl_sales_target.id',
                'tbl_sales_target.total_participant',
                'tbl_sales_target.total_target',
                DB::raw('(CASE
                            WHEN tbl_sales_target.prog_id is null THEN tbl_main_prog.prog_name
                            ELSE program.program_name
                        END) AS program_name'),
                DB::raw('YEAR(tbl_sales_target.month_year) AS year'),
                DB::raw('MONTHNAME(tbl_sales_target.month_year) AS month'),
            )
        )->filterColumn(
            'year',
            function ($query, $keyword) {
                $sql = 'YEAR(tbl_sales_target.month_year) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )->filterColumn(
            'month',
            function ($query, $keyword) {
                $sql = 'MONTHNAME(tbl_sales_target.month_year) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )
            ->filterColumn(
                'program_name',
                function ($query, $keyword) {
                    $sql = '(CASE
                        WHEN tbl_sales_target.prog_id is null THEN tbl_main_prog.prog_name
                        ELSE program.program_name
                    END) like ? ';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->make(true);
    }

    public function getAllSalesTarget()
    {
        return SalesTarget::all();
    }

    public function getSalesTargetById($salesTargetId)
    {
        return SalesTarget::find($salesTargetId);
    }

    public function deleteSalesTarget($salesTargetId)
    {
        return SalesTarget::destroy($salesTargetId);
    }

    public function createSalesTarget(array $salesTargets)
    {
        return SalesTarget::create($salesTargets);
    }

    public function updateSalesTarget($salesTargetId, array $newSalesTargets)
    {
        return tap(SalesTarget::find($salesTargetId))->update($newSalesTargets);
    }
}

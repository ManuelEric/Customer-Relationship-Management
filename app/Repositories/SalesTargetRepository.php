<?php

namespace App\Repositories;

use App\Interfaces\SalesTargetRepositoryInterface;
use App\Models\ClientProgram;
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
        })->when($filter['qdate'], function ($query) use ($filter) {
            $query->whereMonth('month_year', date('m', strtotime($filter['qdate'])))->whereYear('month_year', date('Y', strtotime($filter['qdate'])));
        })->select([
            DB::raw("CAST(YEAR(month_year) AS VARCHAR(4)) + '-' + right('00' + CAST(MONTH(month_year) AS VARCHAR(2)), 2) as month_year"),
            DB::raw('SUM(total_participant) as total_participant'),
            DB::raw('SUM(total_target) as total_target'),
        ])->groupBy(DB::raw("CAST(YEAR(month_year) AS VARCHAR(4)) + '-' + right('00' + CAST(MONTH(month_year) AS VARCHAR(2)), 2)"))->first();
    }

    public function getMonthlySalesActual($programId, $filter)
    {
        $userId = null;
        if (isset($filter['quuid'])) {
            $user = User::where('uuid', $filter['quuid'])->first();
            $userId = $user->id;
        }

        return ClientProgram::leftJoin('tbl_inv', 'tbl_inv.clientprog_id', '=', 'tbl_client_prog.clientprog_id')->when($programId, function ($query) use ($programId) {
            $query->where('prog_id', $programId);
        })->when($userId, function ($query) use ($userId) {
            $query->where('tbl_client_prog.empl_id', $userId);
        })->when($filter['qdate'], function ($query) use ($filter) {
            $query->whereMonth('tbl_client_prog.success_date', date('m', strtotime($filter['qdate'])))->whereYear('tbl_client_prog.success_date', date('Y', strtotime($filter['qdate'])));
        })->when(isset($filter['quuid']), function ($q) use ($userId) {
            $q->where('tbl_client_prog.empl_id', $userId);
        })->select([
            DB::raw('COUNT(*) as total_participant'),
            DB::raw('SUM(tbl_inv.inv_totalprice_idr) as total_target')
        ])->first();
    }

    public function getSalesDetail($programId, $filter)
    {
        $userId = null;
        if (isset($filter['quuid'])) {
            $user = User::where('uuid', $filter['quuid'])->first();
            $userId = $user->id;
        }

        $usingProgramId = $programId ? true : false;
        $usingFilterDate = $filter['qdate'] ? true : false;
        $usingUuid = $userId ? true : false;

        return ClientProgram::
                leftJoin('tbl_inv', 'tbl_inv.clientprog_id', '=', 'tbl_client_prog.clientprog_id')->
                leftJoin('tbl_prog as cp_p', 'cp_p.prog_id', '=', 'tbl_client_prog.prog_id')->
                leftJoin('tbl_main_prog as cp_mp', 'cp_mp.id', '=', 'cp_p.main_prog_id')->
                leftJoin('tbl_sales_target', 'tbl_sales_target.prog_id', '=', 'cp_p.prog_id')->
                when($usingProgramId, function ($query) use ($programId) {
                    $query->where('prog_id', $programId);
                })->
                when($usingFilterDate, function ($query) use ($filter) {
                    $query->where(function ($q) use ($filter) {
                        $q->whereMonth('tbl_client_prog.success_date', date('m', strtotime($filter['qdate'])))->whereYear('tbl_client_prog.success_date', date('Y', strtotime($filter['qdate'])));
                    })->orWhere(function ($q) use ($filter) {
                        $q->whereMonth('tbl_sales_target.month_year', date('m', strtotime($filter['qdate'])))->whereYear('tbl_sales_target.month_year', date('Y', strtotime($filter['qdate'])));
                    });
                })->
                when($usingUuid, function ($q) use ($userId) {
                    $q->where('tbl_client_prog.empl_id', $userId);
                })->
                select([
                    'cp_p.prog_id',
                    DB::raw('CONCAT(cp_mp.prog_name COLLATE utf8mb4_unicode_ci, ": ", cp_p.prog_program COLLATE utf8mb4_unicode_ci) as program_name_sales'),
                    DB::raw('(SELECT total_participant FROM tbl_sales_target WHERE tbl_sales_target.prog_id = cp_p.prog_id AND MONTH(month_year) = '.date('m', strtotime($filter['qdate'])).' AND YEAR(month_year) = '.date('Y', strtotime($filter['qdate'])).') as total_target_participant'),
                    DB::raw('(SELECT total_target FROM tbl_sales_target WHERE tbl_sales_target.prog_id = cp_p.prog_id AND MONTH(month_year) = '.date('m', strtotime($filter['qdate'])).' AND YEAR(month_year) = '.date('Y', strtotime($filter['qdate'])).') as total_target'),
                    DB::raw('(SELECT COUNT(*) FROM tbl_client_prog as q_cp WHERE q_cp.prog_id = cp_p.prog_id AND MONTH(q_cp.success_date) = '.date('m', strtotime($filter['qdate'])).' AND YEAR(q_cp.success_date) = '.date('Y', strtotime($filter['qdate'])).') as total_actual_participant'),
                    DB::raw('(SELECT SUM(q_i.inv_totalprice_idr) FROM tbl_client_prog as q_cp LEFT JOIN tbl_inv q_i ON q_i.clientprog_id = q_cp.clientprog_id WHERE q_cp.prog_id = cp_p.prog_id AND MONTH(q_cp.success_date) = '.date('m', strtotime($filter['qdate'])).' AND YEAR(q_cp.success_date) = '.date('Y', strtotime($filter['qdate'])).') as total_actual_amount'),
                ])->groupBy('cp_p.prog_id', DB::raw('CONCAT(cp_mp.prog_name, ": ", cp_p.prog_program)'))->get();
    }

    public function getAllSalesTargetDataTables()
    {
        return datatables::eloquent(
            SalesTarget::leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_sales_target.prog_id')->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')->select(
                'tbl_sales_target.id',
                'tbl_sales_target.total_participant',
                'tbl_sales_target.total_target',
                DB::raw('(CASE
                    WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                    ELSE tbl_prog.prog_program
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
            ->filterColumn('program_name', function($query, $keyword){
                $sql = '(CASE
                        WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(COALESCE(tbl_sub_prog.sub_prog_name COLLATE utf8mb4_unicode_ci, "")," - ",tbl_prog.prog_program COLLATE utf8mb4_unicode_ci)
                        ELSE tbl_prog.prog_program
                    END) like ? ';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->make(true);
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
        return SalesTarget::find($salesTargetId)->update($newSalesTargets);
    }
}

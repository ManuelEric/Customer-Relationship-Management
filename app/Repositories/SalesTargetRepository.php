<?php

namespace App\Repositories;

use App\Interfaces\SalesTargetRepositoryInterface;
use App\Models\SalesTarget;
use DataTables;
use Illuminate\Support\Facades\DB;

class SalesTargetRepository implements SalesTargetRepositoryInterface
{

    public function getAllSalesTargetDataTables()
    {
        return datatables::eloquent(
            SalesTarget::leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_sales_target.prog_id')->
                leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')->
            select(
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
            )->filterColumn('year', function($query, $keyword){
                $sql = 'YEAR(tbl_sales_target.month_year) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->filterColumn('month', function($query, $keyword){
                $sql = 'MONTHNAME(tbl_sales_target.month_year) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )
            ->filterColumn('program_name', function($query, $keyword){
                $sql = '(CASE
                        WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
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

<?php

namespace App\Repositories;

use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Models\Invb2b;
use App\Models\SchoolProgram;
use DataTables;
use Illuminate\Support\Facades\DB;

class InvoiceB2bRepository implements InvoiceB2bRepositoryInterface
{

    public function getAllInvoiceNeededSchDataTables()
    {
        return datatables::eloquent(
            SchoolProgram::leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_sch_prog.sch_id')->
                leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_sch_prog.prog_id')->
                leftJoin('users', 'users.id', '=', 'tbl_sch_prog.empl_id')->
                leftJoin('tbl_invb2b', 'tbl_invb2b.schprog_id', '=', 'tbl_sch_prog.id')->
                select(
                    'tbl_sch.sch_id', 
                    'tbl_sch_prog.id', 
                    'tbl_sch.sch_name as school_name',
                    'tbl_prog.prog_program as program_name',
                    'tbl_sch_prog.success_date',
                    'users.id as pic_id',
                    DB::raw('CONCAT(users.first_name," ",users.last_name) as pic_name'))->
                where('tbl_sch_prog.status', 1)->
                whereNull('tbl_invb2b.schprog_id')
            )->filterColumn('pic_name', function($query, $keyword){
                $sql = 'CONCAT(users.first_name," ",users.last_name) like ?';
                $query->whereRaw($sql, ["%{$keyword}%"]);
                }
            )->make(true);
    }

    public function getAllInvoiceSchDataTables()
    {
        return datatables::eloquent(
            Invb2b::leftJoin('tbl_sch_prog', 'tbl_sch_prog.id', '=', 'tbl_invb2b.schprog_id')->
                leftJoin('tbl_sch', 'tbl_sch_prog.sch_id', '=', 'tbl_sch.sch_id')->
                leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_sch_prog.prog_id')->
                select(
                    'tbl_invb2b.invb2b_num',  
                    'tbl_sch.sch_name as school_name',
                    'tbl_prog.prog_program as program_name',
                    'tbl_invb2b.schprog_id',
                    'tbl_invb2b.invb2b_id',
                    'tbl_invb2b.invb2b_pm',
                    'tbl_invb2b.created_at',
                    'tbl_invb2b.invb2b_duedate',
                    'tbl_invb2b.invb2b_totprice',)->
                where('tbl_sch_prog.status', 1)
            )->make(true);
    }

    public function getInvoiceB2bById($invb2b_num)
    {
        return Invb2b::find($invb2b_num);
    }

    public function deleteInvoiceB2b($invb2b_num)
    {
        return Invb2b::destroy($invb2b_num);
    }

    public function createInvoiceB2b(array $invoices)
    {
        return Invb2b::create($invoices);
    }

    public function updateInvoiceB2b($invb2b_num, array $newInvoices)
    {
        return Invb2b::find($invb2b_num)->update($newInvoices);
    }
}

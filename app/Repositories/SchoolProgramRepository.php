<?php

namespace App\Repositories;

use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Models\SchoolProgram;
use DataTables;
use Illuminate\Support\Facades\DB;

class SchoolProgramRepository implements SchoolProgramRepositoryInterface
{

    public function getAllSchoolProgramsDataTables()
    {
        return Datatables::eloquent(
            SchoolProgram::leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_sch_prog.sch_id')->
                    leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_sch_prog.prog_id')->
                    leftJoin('users', 'users.id', '=', 'tbl_sch_prog.empl_id')->
                    select(
                        'tbl_sch.sch_id', 
                        'tbl_sch_prog.id', 
                        'tbl_sch.sch_name as school_name',
                        'tbl_prog.prog_program as program_name',
                        'tbl_sch_prog.first_discuss',
                        'tbl_sch_prog.participants',
                        'tbl_sch_prog.total_fee',
                        'tbl_sch_prog.status',
                        DB::raw('CONCAT(users.first_name," ",users.last_name) as pic_name')
                    )
        )->filterColumn('pic_name', function($query, $keyword){
            $sql = 'CONCAT(users.first_name," ",users.last_name) like ?';
            $query->whereRaw($sql, ["%{$keyword}%"]);
            }
        )->make(true);
    }

    public function getAllSchoolProgramsBySchoolId($schoolId)
    {
        return SchoolProgram::where('sch_id', $schoolId)->orderBy('id', 'asc')->get();
    }

    public function getSchoolProgramById($schoolProgramId)
    {
        return SchoolProgram::find($schoolProgramId);
    }

    public function deleteSchoolProgram($schoolProgramId)
    {
        return SchoolProgram::destroy($schoolProgramId);
    }

    public function createSchoolProgram(array $schoolPrograms)
    {
        return SchoolProgram::create($schoolPrograms);
    }

    public function updateSchoolProgram($schoolProgramId, array $newPrograms)
    {
        return SchoolProgram::find($schoolProgramId)->update($newPrograms);
    }
}

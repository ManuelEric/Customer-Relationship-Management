<?php

namespace App\Repositories;

use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Models\SchoolProgram;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\DB;

class SchoolProgramRepository implements SchoolProgramRepositoryInterface
{

    public function getAllSchoolProgramsDataTables($filter = null)
    {
        // TODO: Filter by status refund

        return Datatables::eloquent(
            SchoolProgram::leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_sch_prog.sch_id')->leftJoin('tbl_prog', 'tbl_prog.prog_id', '=', 'tbl_sch_prog.prog_id')->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')->leftJoin('users', 'users.id', '=', 'tbl_sch_prog.empl_id')->select(
                'tbl_sch.sch_id',
                'tbl_sch_prog.id',
                'tbl_sch.sch_name as school_name',
                DB::raw('(CASE
                            WHEN tbl_prog.sub_prog_id > 0 THEN CONCAT(tbl_sub_prog.sub_prog_name," - ",tbl_prog.prog_program)
                            ELSE tbl_prog.prog_program
                        END) AS program_name'),
                'tbl_sch_prog.first_discuss',
                'tbl_sch_prog.participants',
                'tbl_sch_prog.total_fee',
                'tbl_sch_prog.status',
                'tbl_sch_prog.success_date',
                'tbl_sch_prog.start_program_date',
                'tbl_sch_prog.end_program_date',
                'users.id as pic_id',
                DB::raw('CONCAT(users.first_name," ",COALESCE(users.last_name, "")) as pic_name')
            )
                ->when($filter && isset($filter['school_name']), function ($query) use ($filter) {
                    $query->whereIn('tbl_sch.sch_name', $filter['school_name']);
                })
                ->when($filter && isset($filter['program_name']), function ($query) use ($filter) {
                    $query->whereIn('tbl_prog.prog_program', $filter['program_name']);
                })
                ->when($filter && isset($filter['pic']), function ($query) use ($filter) {
                    $query->whereIn('users.id', $filter['pic']);
                })
                ->when($filter && isset($filter['status']) && !isset($filter['start_date']) && !isset($filter['end_date']), function ($query) use ($filter) {
                    $query->whereIn('tbl_sch_prog.status', $filter['status']);
                })
                ->when($filter && isset($filter['start_date']) && isset($filter['end_date']), function ($query) use ($filter) {

                    if (isset($filter['status'])) {

                        if (count($filter['status']) == 1) {

                            // Status == success
                            if ($filter['status'][0] == 1) {
                                $query->whereDate('tbl_sch_prog.start_program_date', '>=', $filter['start_date'])
                                    ->whereDate('tbl_sch_prog.end_program_date', '<=', $filter['end_date'])
                                    ->where('tbl_sch_prog.status', $filter['status'][0]);

                                // Status == denied
                            } else if ($filter['status'][0] == 2) {
                                $query->whereDate('tbl_sch_prog.denied_date', '>=', $filter['start_date'])
                                    ->whereDate('tbl_sch_prog.denied_date', '<=', $filter['end_date'])
                                    ->where('tbl_sch_prog.status', $filter['status'][0]);

                                // Status == refund
                            } else if ($filter['status'][0] == 3) {
                                $query->whereDate('tbl_sch_prog.refund_date', '>=', $filter['start_date'])
                                    ->whereDate('tbl_sch_prog.refund_date', '<=', $filter['end_date'])
                                    ->where('tbl_sch_prog.status', $filter['status'][0]);

                                // Status == pending
                            } else if ($filter['status'][0] == 0) {
                                $query->whereDate('tbl_sch_prog.created_at', '>=', $filter['start_date'])
                                    ->whereDate('tbl_sch_prog.created_at', '<=', $filter['end_date'])
                                    ->whereIn('tbl_sch_prog.status', $filter['status']);
                            }
                        } else {
                            $query->whereDate('tbl_sch_prog.created_at', '>=', $filter['start_date'])
                                ->whereDate('tbl_sch_prog.created_at', '<=', $filter['end_date'])
                                ->whereIn('tbl_sch_prog.status', $filter['status']);
                        }
                    } else {

                        $query->whereDate('tbl_sch_prog.created_at', '>=', $filter['start_date'])
                            ->whereDate('tbl_sch_prog.created_at', '<=', $filter['end_date']);
                    }
                })
                ->when($filter && isset($filter['start_date']) && !isset($filter['end_date']), function ($query) use ($filter) {

                    if (isset($filter['status'])) {

                        if (count($filter['status']) == 1) {

                            // Status == success
                            if ($filter['status'][0] == 1) {
                                $query->whereDate('tbl_sch_prog.success_date', '>=', $filter['start_date'])
                                    ->where('tbl_sch_prog.status', $filter['status'][0]);


                                // Status == denied
                            } else if ($filter['status'][0] == 2) {
                                $query->whereDate('tbl_sch_prog.denied_date', '>=', $filter['start_date'])
                                    ->where('tbl_sch_prog.status', $filter['status'][0]);

                                // Status == refund
                            } else if ($filter['status'][0] == 3) {
                                $query->whereDate('tbl_sch_prog.refund_date', '>=', $filter['start_date'])
                                    ->where('tbl_sch_prog.status', $filter['status'][0]);


                                // Status == pending
                            } else if ($filter['status'][0] == 0) {
                                $query->whereDate('tbl_sch_prog.created_at', '>=', $filter['start_date'])
                                    ->where('tbl_sch_prog.status', $filter['status'][0]);
                            }
                        } else {
                            $query->whereDate('tbl_sch_prog.created_at', '>=', $filter['start_date'])
                                ->where('tbl_sch_prog.status', $filter['status'][0]);
                        }
                    } else {
                        $query->whereDate('tbl_sch_prog.created_at', '>=', $filter['start_date']);
                    }
                })
                ->when($filter && isset($filter['end_date']) && !isset($filter['start_date']), function ($query) use ($filter) {

                    if (isset($filter['status'])) {

                        if (count($filter['status']) == 1) {

                            // Status == success
                            if ($filter['status'][0] == 1) {
                                $query->whereDate('tbl_sch_prog.success_date', '<=', $filter['end_date'])
                                    ->where('tbl_sch_prog.status', $filter['status'][0]);


                                // Status == denied
                            } else if ($filter['status'][0] == 2) {
                                $query->whereDate('tbl_sch_prog.denied_date', '<=', $filter['end_date'])
                                    ->where('tbl_sch_prog.status', $filter['status'][0]);

                                // Status == refund
                            } else if ($filter['status'][0] == 3) {
                                $query->whereDate('tbl_sch_prog.refund_date', '<=', $filter['end_date'])
                                    ->where('tbl_sch_prog.status', $filter['status'][0]);


                                // Status == pending
                            } else if ($filter['status'][0] == 0) {
                                $query->whereDate('tbl_sch_prog.created_at', '<=', $filter['end_date'])
                                    ->where('tbl_sch_prog.status', $filter['status'][0]);
                            }
                        } else {
                            $query->whereDate('tbl_sch_prog.created_at', '<=', $filter['end_date'])
                                ->where('tbl_sch_prog.status', $filter['status'][0]);
                        }
                    } else {
                        $query->whereDate('tbl_sch_prog.created_at', '<=', $filter['end_date']);
                    }
                })

        )->filterColumn(
            'pic_name',
            function ($query, $keyword) {
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

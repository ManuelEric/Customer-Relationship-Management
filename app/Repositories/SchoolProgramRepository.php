<?php

namespace App\Repositories;

use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Models\SchoolProgram;
use App\Models\v1\SchoolProgram as V1SchoolProgram;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\DB;

class SchoolProgramRepository implements SchoolProgramRepositoryInterface
{

    public function getAllSchoolProgramsDataTables($filter = null)
    {
        $program = SchoolProgram::with('program');

        return Datatables::eloquent(
            SchoolProgram::leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_sch_prog.sch_id')->leftJoin('users', 'users.id', '=', 'tbl_sch_prog.empl_id')->leftJoin('program', 'program.prog_id', '=', 'tbl_sch_prog.prog_id')
                ->select(
                    'tbl_sch.sch_id',
                    'tbl_sch_prog.id',
                    'tbl_sch_prog.prog_id',
                    'tbl_sch.sch_name as school_name',
                    'tbl_sch_prog.first_discuss',
                    'tbl_sch_prog.participants',
                    'tbl_sch_prog.total_fee',
                    'tbl_sch_prog.status',
                    'tbl_sch_prog.success_date',
                    'tbl_sch_prog.start_program_date',
                    'tbl_sch_prog.end_program_date',
                    'program.program_name',
                    'users.id as pic_id',
                    DB::raw('CONCAT(users.first_name," ",COALESCE(users.last_name, "")) as pic_name')
                )
                ->when($filter && isset($filter['school_name']), function ($query) use ($filter) {
                    $query->whereIn('tbl_sch.sch_name', $filter['school_name']);
                })
                ->when($filter && isset($filter['program_name']), function ($query) use ($filter) {
                    $query->whereIn('program.program_name', $filter['program_name']);
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

    public function getAllSchoolProgramByStatusAndMonth($status, $monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        return SchoolProgram::leftJoin('tbl_sch', 'tbl_sch.sch_id', '=', 'tbl_sch_prog.sch_id')
            ->leftJoin('program', 'program.prog_id', '=', 'tbl_sch_prog.prog_id')
            // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
            ->select(
                'tbl_sch_prog.status',
                'tbl_sch.sch_name as school_name',
                'program.program_name'
            )

            # Status (4: Accepted, 5: Cancel) Temporary by created_at
            ->whereYear(
                DB::raw('(CASE
                            WHEN tbl_sch_prog.status = 0 THEN tbl_sch_prog.created_at
                            WHEN tbl_sch_prog.status = 1 THEN tbl_sch_prog.success_date
                            WHEN tbl_sch_prog.status = 2 THEN tbl_sch_prog.denied_date
                            WHEN tbl_sch_prog.status = 3 THEN tbl_sch_prog.refund_date
                            WHEN tbl_sch_prog.status = 4 THEN tbl_sch_prog.accepted_date
                            WHEN tbl_sch_prog.status = 5 THEN tbl_sch_prog.cancel_date
                        END)'),
                '=',
                $year
            )
            ->whereMonth(
                DB::raw('(CASE
                            WHEN tbl_sch_prog.status = 0 THEN tbl_sch_prog.created_at
                            WHEN tbl_sch_prog.status = 1 THEN tbl_sch_prog.success_date
                            WHEN tbl_sch_prog.status = 2 THEN tbl_sch_prog.denied_date
                            WHEN tbl_sch_prog.status = 3 THEN tbl_sch_prog.refund_date
                            WHEN tbl_sch_prog.status = 4 THEN tbl_sch_prog.accepted_date
                            WHEN tbl_sch_prog.status = 5 THEN tbl_sch_prog.cancel_date
                        END)'),
                '=',
                $month
            )
            ->where('tbl_sch_prog.status', $status)
            ->get();
    }

    public function getStatusSchoolProgramByMonthly($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        return SchoolProgram::select(
            'status',
            DB::raw('SUM(total_fee) as total_fee'),
            DB::raw('COUNT(*) as count_status')

            # Status (4: Accepted, 5: Cancel) Temporary by created_at
        )->whereYear(
            DB::raw('(CASE
                            WHEN status = 0 THEN created_at
                            WHEN status = 1 THEN success_date
                            WHEN status = 2 THEN denied_date
                            WHEN status = 3 THEN refund_date
                            WHEN status = 4 THEN accepted_date
                            WHEN status = 5 THEN cancel_date
                        END)'),
            '=',
            $year
        )
            ->whereMonth(
                DB::raw('(CASE
                            WHEN status = 0 THEN created_at
                            WHEN status = 1 THEN success_date
                            WHEN status = 2 THEN denied_date
                            WHEN status = 3 THEN refund_date
                            WHEN status = 4 THEN accepted_date
                            WHEN status = 5 THEN cancel_date
                        END)'),
                '=',
                $month
            )
            ->groupBy('status')
            ->get();
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

    public function createSchoolPrograms(array $schoolPrograms)
    {
        return SchoolProgram::insert($schoolPrograms);
    }

    public function updateSchoolProgram($schoolProgramId, array $newPrograms)
    {
        return SchoolProgram::find($schoolProgramId)->update($newPrograms);
    }

    public function getReportSchoolPrograms($start_date = null, $end_date = null)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        if (isset($start_date) && isset($end_date)) {
            return SchoolProgram::where('status', 1)
                ->whereDate('success_date', '>=', $start_date)
                ->whereDate('success_date', '<=', $end_date)

                ->get();
        } else if (isset($start_date) && !isset($end_date)) {
            return SchoolProgram::where('status', 1)
                ->whereDate('success_date', '>=', $start_date)
                ->get();
        } else if (!isset($start_date) && isset($end_date)) {
            return SchoolProgram::where('status', 1)
                ->whereDate('success_date', '<=', $end_date)
                ->get();
        } else {
            return SchoolProgram::where('status', 1)
                ->whereBetween('success_date', [$firstDay, $lastDay])
                ->get();
        }
    }

    public function getTotalSchoolProgramComparison($startYear, $endYear)
    {
        $start = SchoolProgram::select(DB::raw("'start' as 'type'"), DB::raw('count(id) as count'), DB::raw('sum(total_fee) as total_fee'))
            ->where('status', 1)
            ->whereYear('success_date', $startYear);

        $end = SchoolProgram::select(DB::raw("'end' as 'type'"), DB::raw('count(id) as count'), DB::raw('sum(total_fee) as total_fee'))
            ->where('status', 1)
            ->whereYear('success_date', $endYear)
            ->union($start)
            ->get();

        return $end;
    }

    public function getSchoolProgramComparison($startYear, $endYear)
    {
        return SchoolProgram::leftJoin('program', 'program.prog_id', '=', 'tbl_sch_prog.prog_id')
            // ->leftJoin('tbl_sub_prog', 'tbl_sub_prog.id', '=', 'tbl_prog.sub_prog_id')
            ->select(
                'tbl_sch_prog.prog_id',
                'program.program_name',
                DB::raw("'School Program' as type"),
                DB::raw('(CASE 
                            WHEN SUM(participants) is null THEN 0
                            ELSE SUM(participants)
                        END) as participants'),
                DB::raw('DATE_FORMAT(success_date, "%Y") as year'),
                DB::raw("SUM(total_fee) as total"),
                DB::raw('count(tbl_sch_prog.prog_id) as count_program')
            )
            ->where('status', 1)
            // ->whereYear('success_date', '=', $startYear)
            ->whereYear(
                'success_date',
                '=',
                DB::raw('(case year(success_date)
                                when ' . $startYear . ' then ' . $startYear . '
                                when ' . $endYear . ' then ' . $endYear . '
                            end)')
            )
            ->groupBy('tbl_sch_prog.prog_id')
            ->groupBy(DB::raw('year(success_date)'))
            ->get();
    }

    # CRM
    public function getAllSchoolProgramFromCRM()
    {
        return V1SchoolProgram::all();
    }
}

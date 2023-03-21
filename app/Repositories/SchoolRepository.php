<?php

namespace App\Repositories;

use App\Interfaces\SchoolRepositoryInterface;
use App\Models\School;
use App\Models\V1\School as V1School;
use Carbon\Carbon;
use App\Models\SchoolDetail;
use DataTables;

class SchoolRepository implements SchoolRepositoryInterface
{

    public function getAllSchoolDataTables()
    {
        return Datatables::eloquent(School::query())->rawColumns(['sch_location'])
            ->addColumn('curriculum', function ($data) {
                $no = 1;
                $curriculums = '';
                foreach ($data->curriculum as $curriculum) {
                    if ($no == 1)
                        $curriculums = $curriculum->name;
                    else
                        $curriculums .= ', ' . $curriculum->name;

                    $no++;
                }
                return $curriculums;
            })
            ->filterColumn('curriculum', function ($query, $keyword) {
                $query->whereHas('curriculum', function ($q) use ($keyword) {
                    $q->where('name', 'like', '%'.$keyword.'%');
                });
            })
            ->make(true);
    }

    public function getAllSchools()
    {
        return School::orderBy('sch_id', 'asc')->get();
    }

    public function getCountTotalSchoolByMonthly($monthYear)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        return School::whereYear('created_at', '=', $year)
            ->whereMonth('created_at', '=', $month)->count();
    }

    public function getSchoolById($schoolId)
    {
        return School::whereSchoolId($schoolId);
    }

    public function getSchoolByName($schoolName)
    {
        return School::whereSchoolName($schoolName);
        // return School::where('sch_name', $schoolName)->first();
    }

    public function deleteSchool($schoolId)
    {
        return School::whereSchoolId($schoolId)->delete();
    }

    public function createSchool(array $schoolDetails)
    {
        return School::create($schoolDetails);
    }

    public function attachCurriculum($schoolId, array $curriculums)
    {
        $school = School::whereSchoolId($schoolId);
        return $school->curriculum()->attach($curriculums);
    }

    public function createSchools(array $schoolDetails)
    {
        return School::insert($schoolDetails);
    }

    public function updateSchool($schoolId, array $newDetails)
    {
        return School::whereSchoolId($schoolId)->update($newDetails);
    }

    public function cleaningSchool()
    {
        School::where('sch_type', '=', '-')->update(
            [
                'sch_type' => null
            ]
        );

        School::where('sch_curriculum', '=', '')->update(
            [
                'sch_curriculum' => null
            ]
        );

        School::where('sch_mail', '=', '')->update(
            [
                'sch_mail' => null
            ]
        );

        School::where('sch_phone', '=', '')->update(
            [
                'sch_phone' => null
            ]
        );

        School::where('sch_insta', '=', '-')->orWhere('sch_insta', '=', '')->update(
            [
                'sch_insta' => null
            ]
        );

        School::where('sch_city', '=', '-')->orWhere('sch_city', '=', '')->update(
            [
                'sch_city' => null
            ]
        );

        School::where('sch_location', '=', '-')->orWhere('sch_location', '=', '')->update(
            [
                'sch_location' => null
            ]
        );
    }

    public function cleaningSchoolDetail()
    {
        SchoolDetail::where('schdetail_fullname', '=', '')->update(
            [
                'schdetail_fullname' => null
            ]
        );

        SchoolDetail::where('schdetail_email', '=', '')->update(
            [
                'schdetail_email' => null
            ]
        );

        SchoolDetail::where('schdetail_phone', '=', '')->orWhere('schdetail_phone', '=', '-')->update(
            [
                'schdetail_phone' => null
            ]
        );
    }

    public function getReportNewSchool($start_date = null, $end_date = null)
    {
        $firstDay = Carbon::now()->startOfMonth()->toDateString();
        $lastDay = Carbon::now()->endOfMonth()->toDateString();

        if (isset($start_date) && isset($end_date)) {
            return School::whereDate('created_at', '>=', $start_date)
                ->whereDate('created_at', '<=', $end_date)
                ->get();
        } else if (isset($start_date) && !isset($end_date)) {
            return School::whereDate('created_at', '>=', $start_date)
                ->get();
        } else if (!isset($start_date) && isset($end_date)) {
            return School::whereDate('created_at', '<=', $end_date)
                ->get();
        } else {
            return School::whereBetween('created_at', [$firstDay, $lastDay])
                ->get();
        }
    }

    # CRM
    public function getAllSchoolFromV1()
    {
        return V1School::all();
    }
}

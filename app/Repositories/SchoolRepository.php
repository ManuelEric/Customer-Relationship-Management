<?php

namespace App\Repositories;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\SchoolCurriculumRepositoryInterface;
use App\Models\School;
use App\Models\SchoolAliases;
use App\Models\V1\School as V1School;
use Carbon\Carbon;
use App\Models\SchoolDetail;
use DataTables;
use Illuminate\Support\Facades\DB;

class SchoolRepository implements SchoolRepositoryInterface
{
    use CreateCustomPrimaryKeyTrait;

    private SchoolCurriculumRepositoryInterface $schoolCurriculumRepository;

    public function __construct(SchoolCurriculumRepositoryInterface $schoolCurriculumRepository)
    {
        $this->schoolCurriculumRepository = $schoolCurriculumRepository;
    }

    public function getAllSchoolDataTables($isRaw = false)
    {
        $query = School::
                    when($isRaw, function ($subQuery) {
                        $subQuery->isNotVerified();
                    }, function ($subQuery) {
                        $subQuery->isVerified();
                    });

        return Datatables::eloquent($query)->rawColumns(['sch_location'])
            ->addColumn('sch_type_text', function ($data) {
                return str_replace('_', ' ', $data->sch_type);
            })
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
                    $q->where('name', 'like', '%' . $keyword . '%');
                });
            })
            ->make(true);
    }

    public function getAllSchools()
    {
        return School::orderBy('sch_id', 'asc')->groupBy('sch_name')->get();
    }

    public function getVerifiedSchools()
    {
        return School::where('status', 1)->where('is_verified', 'Y')->orderBy('sch_id', 'asc')->groupBy('sch_name')->get();
    }

    public function getSchoolByMonthly($monthYear, $type)
    {
        $year = date('Y', strtotime($monthYear));
        $month = date('m', strtotime($monthYear));

        $query = School::query();

        if ($type == 'all') {
            $query->whereYear('created_at', '<=', $year)
                ->whereMonth('created_at', '<=', $month);
        } else {
            $query->whereYear('created_at', '=', $year)
                ->whereMonth('created_at', '=', $month);
        }

        switch ($type) {
            case 'all':
                return $query->count();
                break;
            case 'monthly':
                return $query->count();
                break;
            case 'list':
                return $query->get();
                break;
        }
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

    public function getSchoolByAlias($alias)
    {
        return School::where('sch_name', 'like', '%'.$alias.'%')->orWhereHas('aliases', function ($subQuery) use ($alias) {
            $subQuery->where('alias', 'like', '%'.$alias.'%');
        })->get();
    }

    public function findDeletedSchoolById($schoolId)
    {
        return School::onlyTrashed()->where('sch_id', $schoolId)->first();
    }

    public function restoreSchool($schoolId)
    {
        return School::where('sch_id', $schoolId)->withTrashed()->restore();
    }

    public function getAliasBySchool($schoolId)
    {
        return SchoolAliases::where('sch_id', $schoolId)->get();
    }

    public function deleteSchool($schoolId)
    {
        return School::whereSchoolId($schoolId)->forceDelete();
    }

    public function moveToTrash($schoolId)
    {
        return School::whereSchoolId($schoolId)->delete();
    }

    public function moveBulkToTrash(array $schoolIds)
    {
        return School::whereIn('sch_id', $schoolIds)->delete();
    }

    public function createSchool(array $schoolDetails)
    {
        return School::create($schoolDetails);
    }

    public function createSchoolIfNotExists(array $schoolDetails, array $schoolCurriculums)
    {
        # find request school name from databases
        if ($school = $this->getSchoolByName($schoolDetails['sch_name']))
            return $school;

        $last_id = School::max('sch_id');
        $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
        $school_id_with_label = 'SCH-' . $this->add_digit((int)$school_id_without_label + 1, 4);

        # insert school
        $school = $this->createSchool(['sch_id' => $school_id_with_label] + $schoolDetails);
        $school_id_after_insert = $school->sch_id;

        # insert school curriculum
        $this->schoolCurriculumRepository->createSchoolCurriculum($school_id_after_insert, $schoolCurriculums);

        return $school;
    }

    public function findSchoolByTerms($searchTerms)
    {
        # using fuzzy matching
        return School::whereRaw('sch_name like ?', ["%{$searchTerms}%"])->orWhereHas('aliases', function ($subQuery) use ($searchTerms) {
            $subQuery->whereRaw('alias like ?', ["%{$searchTerms}%"]);
        })->get();
    }

    public function attachCurriculum($schoolId, array $curriculums)
    {
        $school = School::whereSchoolId($schoolId);
        return $school->curriculum()->attach($curriculums);
    }

    public function getDuplicateSchools()
    {
        return School::isVerified()->select([
                DB::raw('COUNT(*) as count'),
                'tbl_sch.sch_name',
            ])->groupBy('sch_name')->havingRaw('count > 1')->get();
    }

    public function getDuplicateUnverifiedSchools()
    {
        return School::isNotVerified()->select([
            DB::raw('COUNT(*) as count'),
            'tbl_sch.sch_name',
        ])->groupBy('sch_name')->havingRaw('count > 1')->get();
    }

    public function getUnverifiedSchools()
    {
        return School::isNotVerified();
    }

    public function findUnverifiedSchool($schoolId)
    {
        return School::isNotVerified()->whereSchoolId($schoolId);
    }

    public function findVerifiedSchool($schoolId)
    {
        return School::isVerified()->whereSchoolId($schoolId);
    }

    public function getDeletedSchools($asDatatables = false)
    {
        $query = School::onlyTrashed();
        
        return $asDatatables === false ? $query->get() : $query;
    }

    public function getDataTables($model)
    {
        return DataTables::of($model)->
                addColumn('sch_type_text', function ($data) {
                    return str_replace('_', ' ', $data->sch_type);
                })->
                addColumn('curriculum', function ($data) {
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
                })->
                filterColumn('curriculum', function ($query, $keyword) {
                    $query->whereHas('curriculum', function ($q) use ($keyword) {
                        $q->where('name', 'like', '%' . $keyword . '%');
                    });
                })->make(true);
    }

    public function createSchools(array $schoolDetails)
    {
        return School::insert($schoolDetails);
    }

    public function updateSchool($schoolId, array $newDetails)
    {
        return School::whereSchoolId($schoolId)->update($newDetails);
    }

    public function updateSchools(array $schoolIds, array $newDetails)
    {
        return School::whereIn('sch_id', $schoolIds)->update($newDetails);
    }

    public function cleaningSchool()
    {
        School::where('sch_type', '=', '-')->update(
            [
                'sch_type' => null
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

        SchoolDetail::where('schdetail_grade', '=', '')->where('schdetail_grade', '=', ' ')->orWhereNull('schdetail_grade')->update(
            [
                'schdetail_grade' => null
            ]
        );

        SchoolDetail::where('schdetail_position', '=', '')->update(
            [
                'schdetail_position' => null
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

    public function getFeederSchools($eventId)
    {
        $schools = School::join('tbl_client as c', 'c.sch_id', '=', 'tbl_sch.sch_id')
                            ->join('tbl_client_event as ce', DB::raw('(CASE WHEN ce.child_id is NULL THEN ce.client_id ELSE ce.child_id END)'), '=', 'c.id')
                            ->join('tbl_client_prog as cp', 'cp.client_id', '=', 'c.id')
                            ->join('tbl_prog as p', 'p.prog_id', '=', 'cp.prog_id')
                            ->where('ce.event_id', $eventId)->where('p.main_prog_id', '=', 1)
        ->select([
            'tbl_sch.sch_name',
            'tbl_sch.sch_location',
            'c.id',
            'c.first_name',
            // DB::raw('count(*) as school_has_sent_student')
        ])->distinct('c.first_name')->
            // groupBy(['c.sch_id'])->
            get();

        $sum_students = 0;
        foreach ($schools as $school) {
            $response[$school->sch_name] = $sum_students++;
        }

        return $response ?? null;
    }

    public function getUncompeteSchools()
    {
        return School::whereNull('sch_type')->get();
    }

    # CRM v1
    public function getAllSchoolFromV1()
    {
        return V1School::all();
    }

    # alias
    public function createNewAlias($aliasDetail)
    {
        return SchoolAliases::create($aliasDetail);
    }

    public function deleteAlias($aliasid)
    {
        return SchoolAliases::find($aliasid)->delete();
    }
}

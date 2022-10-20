<?php

namespace App\Repositories;

use App\Interfaces\SchoolRepositoryInterface;
use App\Models\School;
use App\Models\SchoolDetail;
use DataTables;

class SchoolRepository implements SchoolRepositoryInterface 
{

    public function getAllSchoolDataTables()
    {
        return Datatables::eloquent(School::query())->make(true);
    }

    public function getAllSchools()
    {
        return School::orderBy('sch_id', 'asc')->get();
    }

    public function getSchoolById($schoolId)
    {
        return School::whereSchoolId($schoolId);
    }

    public function deleteSchool($schoolId)
    {
        return School::whereSchoolId($schoolId)->delete();
    }

    public function createSchool(array $schoolDetails) 
    {
        return School::create($schoolDetails);
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
}
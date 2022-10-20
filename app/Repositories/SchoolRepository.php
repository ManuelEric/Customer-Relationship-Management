<?php

namespace App\Repositories;

use App\Interfaces\SchoolRepositoryInterface;
use App\Models\School;
use App\Models\SchoolDetail;

class SchoolRepository implements SchoolRepositoryInterface 
{

    public function getAllSchools()
    {
        return School::orderBy('sch_id', 'asc')->get();
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
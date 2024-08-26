<?php

namespace App\Repositories;

use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Models\SchoolDetail;
use App\Models\v1\SchoolDetail as V1SchoolDetail;
use DataTables;

class SchoolDetailRepository implements SchoolDetailRepositoryInterface
{

    public function getAllSchoolDetailDataTables($schoolId)
    {
        return datatables::eloquent(SchoolDetail::where('sch_id', $schoolId)->get())->make(true);
    }

    public function getAllSchoolDetails()
    {
        return SchoolDetail::orderBy('schdetail_id', 'asc')->get();
    }

    public function getAllSchoolDetailsById($schoolId)
    {
        return SchoolDetail::where('sch_id', $schoolId)->orderBy('schdetail_id', 'asc')->get();
    }

    public function getSchoolDetailById($schoolDetailId)
    {
        return SchoolDetail::find($schoolDetailId);
    }

    public function deleteAgendaSpeaker($schoolId, $eventId)
    {
        $speakers = SchoolDetail::where('sch_id', $schoolId)->get();
        foreach ($speakers as $speaker) {
            $speaker->as_event_speaker()->detach($eventId);
        }
    }

    public function deleteSchoolDetail($schoolDetailId)
    {
        return SchoolDetail::destroy($schoolDetailId);
    }

    public function createSchoolDetail(array $schoolDetails)
    {
        return SchoolDetail::insert($schoolDetails);
    }

    public function updateSchoolDetail($schoolDetailId, array $newDetails)
    {
        return SchoolDetail::find($schoolDetailId)->update($newDetails);
    }

    # CRM
    public function getAllSchoolDetailFromCRM()
    {
        return V1SchoolDetail::all();
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolRawRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\SchoolRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolRawController extends Controller
{
    use LoggingTrait;
    protected SchoolRepositoryInterface $schoolRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax())
            return $this->schoolRepository->getAllSchoolDataTables(true);

        $duplicates_schools = $this->schoolRepository->getDuplicateUnverifiedSchools();
        $duplicates_schools_string = $this->convertDuplicatesSchoolAsString($duplicates_schools);

        return view('pages.instance.school.raw.index')->with(
            [
                'duplicates_schools_string' => $duplicates_schools_string,
                'duplicates_schools' => $duplicates_schools->pluck('sch_name')->toArray()
            ]
        );
    }

    private function convertDuplicatesSchoolAsString($schools)
    {
        $response = '';
        foreach ($schools as $school) {

            $response .= ', '.$school->sch_name;

        }

        return $response;
    }

    public function create(Request $request)
    {
        return view('pages.instance.school.raw.form-new');
    }

    public function update(StoreSchoolRawRequest $request)
    {
        $schoolDetails = $request->only([
            'sch_name',
            'sch_type',
            'sch_location',
            'sch_score',
        ]);

        $schoolId = $request->route('raw');
        $oldSchool = $this->schoolRepository->getSchoolById($schoolId);

        DB::beginTransaction();
        try {

            # insert into school
            $this->schoolRepository->updateSchool($schoolId, $schoolDetails + ['is_verified' => 'Y']);


            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Convert raw school failed : ' . $e->getMessage());
            return Redirect::to('instance/school/raw')->withError('Failed to convert school');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'School', Auth::user()->first_name . ' ' . Auth::user()->last_name, $schoolDetails, $oldSchool);

        return Redirect::to('instance/school/raw')->   withSuccess('Convert raw school success');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolRequest;
use App\Interfaces\SchoolRepositoryInterface;
use App\Models\School;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolController extends Controller
{
    protected SchoolRepositoryInterface $schoolRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }

    public function index(): JsonResponse
    {
        return response()->json($this->schoolRepository->getAllSchools());
    }

    public function store(StoreSchoolRequest $request)
    {
        $schoolDetails = $request->only([
            'sch_name',
            'sch_type',
            'sch_curriculum',
            'sch_insta',
            'sch_mail',
            'sch_phone',
            'sch_city',
            'sch_location',
        ]);

        $last_id = School::max('sch_id');
        $school_id_without_label = $this->remove_primarykey_label($last_id, 4);
        $school_id_with_label = 'SCH-' . $this->add_digit($school_id_without_label + 1, 4);

        DB::beginTransaction();
        try {

            $this->schoolRepository->createSchool(['sch_id' => $school_id_with_label] + $schoolDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store school failed : ' . $e->getMessage());
        }

        return Redirect::to('master/school')->withSuccess('School successfully created');
    }

    public function create()
    {
        return view('pages.school.index');
    }
}

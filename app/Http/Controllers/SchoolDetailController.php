<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolDetailRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\SchoolDetailRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolDetailController extends Controller
{
    use CreateCustomPrimaryKeyTrait;

    protected SchoolDetailRepositoryInterface $schoolDetailRepository;

    public function __construct(SchoolDetailRepositoryInterface $schoolDetailRepository)
    {
        $this->schoolDetailRepository = $schoolDetailRepository;
    }

    public function store($school_id, StoreSchoolDetailRequest $request)
    {
        $schoolDetails = $request->only([
            'sch_id',
            'schdetail_fullname',
            'schdetail_email',
            'schdetail_grade',
            'schdetail_position',
            'schdetail_phone',
        ]);

        DB::beginTransaction();
        try {

            $this->schoolDetailRepository->createSchoolDetail(['sch_id' => $school_id] + $schoolDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store school contact person failed : ' . $e->getMessage());
        }

        return Redirect::to('master/school')->withSuccess('School contact person successfully created');
    }
}

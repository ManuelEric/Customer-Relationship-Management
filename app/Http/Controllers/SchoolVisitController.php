<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolVisitRequest;
use App\Interfaces\SchoolVisitRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolVisitController extends Controller
{

    protected SchoolVisitRepositoryInterface $schoolVisitRepository;

    public function __construct(SchoolVisitRepositoryInterface $schoolVisitRepository)
    {
        $this->schoolVisitRepository = $schoolVisitRepository;
    }

    public function store(StoreSchoolVisitRequest $request)
    {
        $schoolId = $request->route('school');

        $visitDetails = $request->only([
            'internal_pic',
            'school_pic',
            'visit_date',
            'notes',
        ]);

        # default status
        $visitDetails['status'] = 'waiting';

        DB::beginTransaction();
        try {

            $this->schoolVisitRepository->createSchoolVisit(['sch_id' => $schoolId] + $visitDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store visit schedule failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to create visit schedule');

        }

        return Redirect::to('instance/school/'.$schoolId)->withSuccess('Visit schedule has been created');
    }

    public function update(Request $request)
    {
        $schoolId = $request->route('school');
        $visitId = $request->route('visit');

        DB::beginTransaction();
        try {

            $this->schoolVisitRepository->updateSchoolVisit($visitId, ['status' => 'visited']);
            DB::commit();
             
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update status school visit failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to update visit schedule');

        }

        return Redirect::to('instance/school/'.$schoolId)->withSuccess('Visit schedule has been updated');
    }

    public function destroy(Request $request)
    {
        $visitId = $request->route('visit');
        $schoolId = $request->route('school');

        DB::beginTransaction();
        try {

            $this->schoolVisitRepository->deleteSchoolVisit($visitId);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Destroy visit schedule failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to cancel visit schedule');

        }

        return Redirect::to('instance/school/'.$schoolId)->withSuccess('Visit schedule has been canceled');

    }
}

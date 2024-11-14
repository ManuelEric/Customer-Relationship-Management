<?php

namespace App\Http\Controllers;

use App\Actions\Schools\Visit\CreateSchoolVisitAction;
use App\Actions\Schools\Visit\DeleteSchoolVisitAction;
use App\Actions\Schools\Visit\UpdateSchoolVisitAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreSchoolVisitRequest;
use App\Interfaces\SchoolVisitRepositoryInterface;
use App\Services\Log\LogService;
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

    public function store(StoreSchoolVisitRequest $request, CreateSchoolVisitAction $createSchoolVisitAction, LogService $log_service)
    {
        $school_id = $request->route('school');

        $visit_details = $request->safe()->only([
            'internal_pic',
            'school_pic',
            'visit_date',
            'notes',
        ]);

        DB::beginTransaction();
        try {

            $created_school_visit = $createSchoolVisitAction->execute($school_id, $visit_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_SCHOOL_VISIT, $e->getMessage(), $e->getLine(), $e->getFile(), $visit_details);

            return Redirect::back()->withError('Failed to create visit schedule');

        }

        # create log success
        $log_service->createSuccessLog(LogModule::STORE_SCHOOL_VISIT, 'New school visit has been added', $created_school_visit->toArray());

        return Redirect::to('instance/school/'.$school_id)->withSuccess('Visit schedule has been created');
    }

    public function update(Request $request, UpdateSchoolVisitAction $updateSchoolVisitAction, LogService $log_service)
    {
        $school_id = $request->route('school');
        $visit_id = $request->route('visit');

        DB::beginTransaction();
        try {

            $updated_school_visit = $updateSchoolVisitAction->execute($visit_id);
            DB::commit();
             
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_SCHOOL_VISIT, $e->getMessage(), $e->getLine(), $e->getFile(), $updated_school_visit->toArray());

            return Redirect::back()->withError('Failed to update visit schedule');

        }

        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_SCHOOL_VISIT, 'School visit has been updated', $updated_school_visit->toArray());

        return Redirect::to('instance/school/'.$school_id)->withSuccess('Visit schedule has been updated');
    }

    public function destroy(Request $request, DeleteSchoolVisitAction $deleteSchoolVisitAction, LogService $log_service)
    {
        $visit_id = $request->route('visit');
        $school_id = $request->route('school');

        DB::beginTransaction();
        try {

            $deleted_school_visit = $deleteSchoolVisitAction->execute($visit_id);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_SCHOOL_VISIT, $e->getMessage(), $e->getLine(), $e->getFile(), $deleted_school_visit->toArray());

            return Redirect::back()->withError('Failed to cancel visit schedule');

        }

        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_SCHOOL_VISIT, 'School visit has been deleted', $deleted_school_visit->toArray());

        return Redirect::to('instance/school/'.$school_id)->withSuccess('Visit schedule has been canceled');

    }
}

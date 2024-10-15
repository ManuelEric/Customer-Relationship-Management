<?php

namespace App\Http\Controllers;

use App\Actions\Majors\CreateMajorAction;
use App\Actions\Majors\DeleteMajorAction;
use App\Actions\Majors\UpdateMajorAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreMajorRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\MajorRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redirect;

class MajorController extends Controller
{
    use LoggingTrait;

    protected MajorRepositoryInterface $majorRepository;

    public function __construct(MajorRepositoryInterface $majorRepository)
    {
        $this->majorRepository = $majorRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->majorRepository->getAllMajorsDataTables();
        }

        return view('pages.master.major.index');
    }

    public function store(StoreMajorRequest $request, CreateMajorAction $createMajorAction, LogService $log_service)
    {
        $new_major_details = $request->safe()->only([
            'name',
            'active'
        ]);

        DB::beginTransaction();
        try {

            $new_major = $createMajorAction->execute($new_major_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_MAJOR, $e->getMessage(), $e->getLine(), $e->getFile(), $new_major_details);

            return Redirect::to('master/major')->withError('Failed to create a new major');
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_MAJOR, 'New major has been Added', $new_major->toArray());

        return Redirect::to('master/major')->withSuccess('Major successfully created');
    }

    public function show(Request $request)
    {
        $major_id = $request->route('major');

        $major = $this->majorRepository->getMajorById($major_id);

        return response()->json(['major' => $major]);
    }

    public function update(StoreMajorRequest $request, UpdateMajorAction $updateMajorAction, LogService $log_service)
    {
        $new_major_details = $request->safe()->only([
            'name',
            'active'
        ]);

        $major_id = $request->route('major');

        DB::beginTransaction();
        try {

            $updated_major = $updateMajorAction->execute($major_id, $new_major_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_MAJOR, $e->getMessage(), $e->getLine(), $e->getFile(), $new_major_details);

            return Redirect::to('master/major')->withError('Failed to update a major');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_MAJOR, 'Major has been updated', $updated_major->toArray());

        return Redirect::to('master/major')->withSuccess('Major successfully updated');
    }

    public function destroy(Request $request, DeleteMajorAction $deleteMajorAction, LogService $log_service)
    {
        $major_id = $request->route('major');
        $old_major = $this->majorRepository->getMajorById($major_id);

        DB::beginTransaction();
        try {

            $deleteMajorAction->execute($major_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            $log_service->createErrorLog(LogModule::DELETE_MAJOR, $e->getMessage(), $e->getLine(), $e->getFile(), $old_major->toArray());

            return Redirect::to('master/major')->withError('Failed to delete a major');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_MAJOR, 'Major has been deleted', $old_major->toArray());

        return Redirect::to('master/major')->withSuccess('Major successfully deleted');
    }
}
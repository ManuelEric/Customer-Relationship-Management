<?php

namespace App\Http\Controllers;

use App\Actions\Curriculums\CreateCurriculumAction;
use App\Actions\Curriculums\DeleteCurriculumAction;
use App\Actions\Curriculums\UpdateCurriculumAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreCurriculumRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\CurriculumRepositoryInterface;
use App\Services\Log\LogService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class CurriculumController extends Controller
{
    use LoggingTrait;

    protected CurriculumRepositoryInterface $curriculumRepository;

    public function __construct(CurriculumRepositoryInterface $curriculumRepository)
    {
        $this->curriculumRepository = $curriculumRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            return $this->curriculumRepository->getAllCurriculumsDataTables();
        }

    
        return view('pages.master.curriculum.index');
    }

    public function store(StoreCurriculumRequest $request, CreateCurriculumAction $createCurriculumAction, LogService $log_service)
    {
        $new_curriculum_details = $request->safe()->only([
            'name',
        ]);

        DB::beginTransaction();
        try {

            $new_curriculum = $createCurriculumAction->execute($new_curriculum_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_CURRICULUM, $e->getMessage(), $e->getLine(), $e->getFile(), $new_curriculum_details);

            return Redirect::to('master/curriculum')->withError('Failed to create a new curriculum');
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_CURRICULUM, 'New curriculum has been added', $new_curriculum->toArray());

        return Redirect::to('master/curriculum')->withSuccess('Curriculum successfully created');
    }

    public function update(StoreCurriculumRequest $request, UpdateCurriculumAction $updateCurriculumAction, LogService $log_service)
    {
        $new_curriculum_details = $request->safe()->only([
            'name',
        ]);

        $curriculum_id = $request->route('curriculum');

        DB::beginTransaction();
        try {

            $new_curriculum = $updateCurriculumAction->execute($curriculum_id, $new_curriculum_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_CURRICULUM, $e->getMessage(), $e->getLine(), $e->getFile(), $new_curriculum_details);

            return Redirect::to('master/curriculum')->withError('Failed to update a curriculum');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_CURRICULUM, 'Curriculum has been updated', $new_curriculum->toArray());

        return Redirect::to('master/curriculum')->withSuccess('Curriculum successfully updated');
    }

    public function edit(Request $request)
    {

        if ($request->ajax()) {
            $curriculum_id = $request->route('curriculum');
            $curriculum = $this->curriculumRepository->getCurriculumById($curriculum_id);

            return response()->json(['curriculum' => $curriculum]);
        }
    }

    public function destroy(Request $request, DeleteCurriculumAction $deleteCurriculumAction, LogService $log_service)
    {
        $curriculum_id = $request->route('curriculum');
        $curriculum = $this->curriculumRepository->getCurriculumById($curriculum_id);

        DB::beginTransaction();
        try {

            $deleteCurriculumAction->execute($curriculum_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_CURRICULUM, $e->getMessage(), $e->getLine(), $e->getFile(), $curriculum->toArray());

            return Redirect::to('master/curriculum')->withError('Failed to delete a curriculum');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_CURRICULUM, 'Curriculum has been deleted', $curriculum->toArray());

        return Redirect::to('master/curriculum')->withSuccess('Curriculum successfully deleted');
    }

}
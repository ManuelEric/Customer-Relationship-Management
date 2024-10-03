<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurriculumRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\CurriculumRepositoryInterface;
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

    public function store(StoreCurriculumRequest $request)
    {
        $curriculum = $request->only([
            'name',
        ]);

        $curriculum['created_at'] = Carbon::now();
        $curriculum['updated_at'] = Carbon::now();

        DB::beginTransaction();
        try {

            $curriculum_created = $this->curriculumRepository->createOneCurriculum($curriculum);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create curriculum failed : ' . $e->getMessage());

            return Redirect::to('master/curriculum')->withError('Failed to create a new curriculum');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Curriculum', Auth::user()->first_name . ' '. Auth::user()->last_name, $curriculum_created);

        return Redirect::to('master/curriculum')->withSuccess('Curriculum successfully created');
    }

    public function update(StoreCurriculumRequest $request)
    {
        $curriculum = $request->only([
            'name',
        ]);

        $curriculum['updated_at'] = Carbon::now();

        $curriculum_id = $request->route('curriculum');
        $old_curriclum = $this->curriculumRepository->getCurriculumById($curriculum_id);

        DB::beginTransaction();
        try {

            $this->curriculumRepository->updateCurriculum($curriculum_id, $curriculum);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update sales target failed : ' . $e->getMessage());
            return Redirect::to('master/curriculum')->withError('Failed to update a curriculum');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Curriculum', Auth::user()->first_name . ' '. Auth::user()->last_name, $curriculum, $old_curriclum);

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

    public function destroy(Request $request)
    {
        $curriculum_id = $request->route('curriculum');
        $curriculum = $this->curriculumRepository->getCurriculumById($curriculum_id);

        DB::beginTransaction();
        try {

            $this->curriculumRepository->deleteCurriculum($curriculum_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete curriculum failed : ' . $e->getMessage());
            return Redirect::to('master/curriculum')->withError('Failed to delete a curriculum');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Curriculum', Auth::user()->first_name . ' '. Auth::user()->last_name, $curriculum);

        return Redirect::to('master/curriculum')->withSuccess('Curriculum successfully deleted');
    }

}
<?php

namespace App\Http\Controllers;

use App\Actions\Assets\UpdateCurriculumAction;
use App\Actions\Curriculums\CreateCurriculumAction;
use App\Actions\Curriculums\DeleteCurriculumAction;
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

    public function store(StoreCurriculumRequest $request, CreateCurriculumAction $createCurriculumAction)
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
            Log::error('Create curriculum failed : ' . $e->getMessage());

            return Redirect::to('master/curriculum')->withError('Failed to create a new curriculum');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Curriculum', Auth::user()->first_name . ' '. Auth::user()->last_name, $new_curriculum);

        return Redirect::to('master/curriculum')->withSuccess('Curriculum successfully created');
    }

    public function update(StoreCurriculumRequest $request, UpdateCurriculumAction $updateCurriculumAction)
    {
        $new_curriculum_details = $request->safe()->only([
            'name',
        ]);

        $curriculum_id = $request->route('curriculum');
        $old_curriclum = $this->curriculumRepository->getCurriculumById($curriculum_id);

        DB::beginTransaction();
        try {

            $new_curriculum = $updateCurriculumAction->execute($curriculum_id, $new_curriculum_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update sales target failed : ' . $e->getMessage());
            return Redirect::to('master/curriculum')->withError('Failed to update a curriculum');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Curriculum', Auth::user()->first_name . ' '. Auth::user()->last_name, $new_curriculum, $old_curriclum);

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

    public function destroy(Request $request, DeleteCurriculumAction $deleteCurriculumAction)
    {
        $curriculum_id = $request->route('curriculum');
        $curriculum = $this->curriculumRepository->getCurriculumById($curriculum_id);

        DB::beginTransaction();
        try {

            $deleteCurriculumAction->execute($curriculum_id);
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
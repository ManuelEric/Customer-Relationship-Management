<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurriculumRequest;
use App\Interfaces\CurriculumRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class CurriculumController extends Controller
{

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

            $this->curriculumRepository->createCurriculum($curriculum);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create curriculum failed : ' . $e->getMessage());

            return $e->getMessage();
            exit;
            return Redirect::to('master/curriculum')->withError('Failed to create a new curriculum');
        }

        return Redirect::to('master/curriculum')->withSuccess('Curriculum successfully created');
    }

    public function update(StoreCurriculumRequest $request)
    {
        $curriculum = $request->only([
            'name',
        ]);

        $curriculum['updated_at'] = Carbon::now();

        $curriculumId = $request->route('curriculum');

        DB::beginTransaction();
        try {

            $this->curriculumRepository->updateCurriculum($curriculumId, $curriculum);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update sales target failed : ' . $e->getMessage());
            return Redirect::to('master/curriculum')->withError('Failed to update a curriculum');
        }

        return Redirect::to('master/curriculum')->withSuccess('Curriculum successfully updated');
    }

    public function edit(Request $request)
    {

        if ($request->ajax()) {
            $curriculumId = $request->route('curriculum');
            $curriculum = $this->curriculumRepository->getCurriculumById($curriculumId);

            return response()->json(['curriculum' => $curriculum]);
        }
    }

    public function destroy(Request $request)
    {
        $curriculumId = $request->route('curriculum');

        DB::beginTransaction();
        try {

            $this->curriculumRepository->deleteCurriculum($curriculumId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete curriculum failed : ' . $e->getMessage());
            return Redirect::to('master/curriculum')->withError('Failed to delete a curriculum');
        }

        return Redirect::to('master/curriculum')->withSuccess('Curriculum successfully deleted');
    }

}
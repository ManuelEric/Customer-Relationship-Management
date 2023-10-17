<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMajorRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\MajorRepositoryInterface;
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

    public function store(StoreMajorRequest $request)
    {
        $majorDetails = $request->only([
            'name',
            'active'
        ]);

        DB::beginTransaction();
        try {

            $newMajor = $this->majorRepository->createMajor($majorDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store major failed : ' . $e->getMessage());
            return Redirect::to('master/major')->withError('Failed to create a new major');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Major', Auth::user()->first_name . ' '. Auth::user()->last_name, $newMajor);

        return Redirect::to('master/major')->withSuccess('Major successfully created');
    }

    public function show(Request $request)
    {
        $majorId = $request->route('major');

        $major = $this->majorRepository->getMajorById($majorId);

        return response()->json(['major' => $major]);
    }

    public function update(StoreMajorRequest $request)
    {
        $majorDetails = $request->only([
            'name',
            'active'
        ]);

        $id = $request->route('major');
        $oldMajor = $this->majorRepository->getMajorById($id);

        DB::beginTransaction();
        try {

            $this->majorRepository->updateMajor($id, $majorDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update major failed : ' . $e->getMessage());
            return Redirect::to('master/major')->withError('Failed to update a major');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Major', Auth::user()->first_name . ' '. Auth::user()->last_name, $majorDetails, $oldMajor);

        return Redirect::to('master/major')->withSuccess('Major successfully updated');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('major');
        $major = $this->majorRepository->getMajorById($id);

        DB::beginTransaction();
        try {

            $this->majorRepository->deleteMajor($id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete major failed : ' . $e->getMessage());
            return Redirect::to('master/major')->withError('Failed to delete a major');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Curriculum', Auth::user()->first_name . ' '. Auth::user()->last_name, $major);

        return Redirect::to('master/major')->withSuccess('Major successfully deleted');
    }
}
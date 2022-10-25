<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMajorRequest;
use App\Interfaces\MajorRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class MajorController extends Controller
{
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

        return view('pages.major.index');
    }

    public function store(StoreMajorRequest $request)
    {
        $majorDetails = $request->only([
            'name'
        ]);

        DB::beginTransaction();
        try {

            $this->majorRepository->createMajor($majorDetails);
            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::error('Store major failed : ' . $e->getMessage());

        }

        return Redirect::to('master/major')->withSuccess('Major successfully created');
    }

    public function update(StoreMajorRequest $request) 
    {
        $majorDetails = $request->only([
            'name'
        ]);

        $id = $request->route('major');

        DB::beginTransaction();
        try {

            $this->majorRepository->updateMajor($id, $majorDetails);
            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::error('Update major failed : ' . $e->getMessage());

        }

        return Redirect::to('master/major')->withSuccess('Major successfully updated');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('major');

        DB::beginTransaction();
        try {

            $this->majorRepository->deleteMajor($id);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete major failed : ' . $e->getMessage());

        }

        return Redirect::to('master/major')->withSuccess('Major successfully deleted');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePositionRequest;
use App\Interfaces\PositionRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class PositionController extends Controller
{
    protected PositionRepositoryInterface $positionRepository;

    public function __construct(PositionRepositoryInterface $positionRepository)
    {
        $this->positionRepository = $positionRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->positionRepository->getAllPositionDataTables();
        }

        return view('pages.position.index');
    }

    public function store(StorePositionRequest $request)
    {
        $positionDetails = $request->only([
            'position_name',
        ]);

        DB::beginTransaction();
        try {

            $this->positionRepository->createPosition($positionDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store position failed : ' . $e->getMessage());
            return Redirect::to('master/position')->withError('Failed to create a new position');

        }

        return Redirect::to('master/position')->withSuccess('Position successfully created');
    }

    public function update(StorePositionRequest $request)
    {
        $positionDetails = $request->only([
            'position_name',
        ]);

        $id = $request->route('position');

        DB::beginTransaction();
        try {

            $this->positionRepository->updatePosition($id, $positionDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update position failed : ' . $e->getMessage());
            return Redirect::to('master/position')->withError('Failed to update position');

        }

        return Redirect::to('master/position')->withSuccess('Position successfully updated');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('position');

        DB::beginTransaction();
        try {

            $this->positionRepository->deletePosition($id);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete position failed : ' . $e->getMessage());
            return Redirect::to('master/position')->withError('Failed to delete position');

        }

        return Redirect::to('master/position')->withSuccess('Position successfully deleted');
    }
}

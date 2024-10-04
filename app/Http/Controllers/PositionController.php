<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePositionRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\PositionRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class PositionController extends Controller
{
    use LoggingTrait;

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

        return view('pages.master.position.index');
    }

    public function store(StorePositionRequest $request)
    {
        $position_details = $request->only([
            'position_name',
        ]);

        DB::beginTransaction();
        try {

            $new_position = $this->positionRepository->createPosition($position_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store position failed : ' . $e->getMessage());
            return Redirect::to('master/position')->withError('Failed to create a new position');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Position', Auth::user()->first_name . ' '. Auth::user()->last_name, $new_position);

        return Redirect::to('master/position')->withSuccess('Position successfully created');
    }

    public function show(Request $request)
    {
        $id = $request->route('position');

        $position = $this->positionRepository->getPositionById($id);

        return response()->json(['position' => $position]);
    }

    public function update(StorePositionRequest $request)
    {
        $position_details = $request->only([
            'position_name',
        ]);

        $id = $request->route('position');
        $old_position = $this->positionRepository->getPositionById($id);

        DB::beginTransaction();
        try {

            $this->positionRepository->updatePosition($id, $position_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update position failed : ' . $e->getMessage());
            return Redirect::to('master/position')->withError('Failed to update position');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Position', Auth::user()->first_name . ' '. Auth::user()->last_name, $position_details, $old_position);

        return Redirect::to('master/position')->withSuccess('Position successfully updated');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('position');
        $position = $this->positionRepository->getPositionById($id);

        DB::beginTransaction();
        try {

            $this->positionRepository->deletePosition($id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete position failed : ' . $e->getMessage());
            return Redirect::to('master/position')->withError('Failed to delete position');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Position', Auth::user()->first_name . ' '. Auth::user()->last_name, $position);

        return Redirect::to('master/position')->withSuccess('Position successfully deleted');
    }
}
<?php

namespace App\Http\Controllers;

use App\Actions\Positions\CreatePositionAction;
use App\Actions\Positions\DeletePositionAction;
use App\Actions\Positions\UpdatePositionAction;
use App\Enum\LogModule;
use App\Http\Requests\StorePositionRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\PositionRepositoryInterface;
use App\Services\Log\LogService;
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

    public function store(StorePositionRequest $request, CreatePositionAction $createPositionAction, LogService $log_service)
    {
        $new_position_details = $request->safe()->only([
            'position_name',
        ]);

        DB::beginTransaction();
        try {

            $new_position = $createPositionAction->execute($new_position_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_POSITION, $e->getMessage(), $e->getLine(), $e->getFile(), $new_position_details);

            return Redirect::to('master/position')->withError('Failed to create a new position');
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_POSITION, 'New position has been added', $new_position->toArray());

        return Redirect::to('master/position')->withSuccess('Position successfully created');
    }

    public function show(Request $request)
    {
        $id = $request->route('position');

        $position = $this->positionRepository->getPositionById($id);

        return response()->json(['position' => $position]);
    }

    public function update(StorePositionRequest $request, UpdatePositionAction $updatePositionAction, LogService $log_service)
    {
        $new_position_details = $request->only([
            'position_name',
        ]);

        $position_id = $request->route('position');
        $old_position = $this->positionRepository->getPositionById($position_id);

        DB::beginTransaction();
        try {

            $updated_position = $updatePositionAction->execute($position_id, $new_position_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_POSITION, $e->getMessage(), $e->getLine(), $e->getFile(), $new_position_details);

            return Redirect::to('master/position')->withError('Failed to update position');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_POSITION, 'Position has been updated', $updated_position->toArray());

        return Redirect::to('master/position')->withSuccess('Position successfully updated');
    }

    public function destroy(Request $request, DeletePositionAction $deletePositionAction, LogService $log_service)
    {
        $position_id = $request->route('position');
        $old_position = $this->positionRepository->getPositionById($position_id);

        DB::beginTransaction();
        try {

            $deletePositionAction->execute($position_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_POSITION, $e->getMessage(), $e->getLine(), $e->getFile(), $old_position->toArray());

            return Redirect::to('master/position')->withError('Failed to delete position');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_POSITION, 'Position has been deleted', $old_position->toArray());

        return Redirect::to('master/position')->withSuccess('Position successfully deleted');
    }
}
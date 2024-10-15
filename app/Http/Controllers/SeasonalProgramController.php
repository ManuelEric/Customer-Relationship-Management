<?php

namespace App\Http\Controllers;

use App\Actions\SeasonalPrograms\CreateSeasonalProgramAction;
use App\Actions\SeasonalPrograms\DeleteSeasonalProgramAction;
use App\Actions\SeasonalPrograms\UpdateSeasonalProgramAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreSeasonalProgramRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SeasonalProgramRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SeasonalProgramController extends Controller
{
    use LoggingTrait;

    protected SeasonalProgramRepositoryInterface $seasonalProgramRepository;
    protected ProgramRepositoryInterface $programRepository;

    public function __construct(SeasonalProgramRepositoryInterface $seasonalProgramRepository, ProgramRepositoryInterface $programRepository)
    {
        $this->seasonalProgramRepository = $seasonalProgramRepository;
        $this->programRepository = $programRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $model = $this->seasonalProgramRepository->getSeasonalPrograms(true);

            return $this->seasonalProgramRepository->getDataTables($model);
        }

        # for create modal
        $programs = $this->programRepository->getAllPrograms();

        return view('pages.master.seasonal-program.index')->with(
            [
                'programs' => $programs
            ]
        );
    }

    public function store(StoreSeasonalProgramRequest $request, CreateSeasonalProgramAction $createSeasonalProgramAction, LogService $log_service)
    {
        $seasonal_program_details = $request->safe()->only([
            'prog_id',
            'start',
            'end',
            'sales_date'
        ]);

        DB::beginTransaction();
        try {
            
            $new_seasonal_program = $createSeasonalProgramAction->execute($seasonal_program_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_SEASONAL_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $seasonal_program_details);

            return Redirect::to('master/seasonal-program')->withError('Failed to create a new seasonal program');

        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_SEASONAL_PROGRAM, 'New seasonal program has been added', $new_seasonal_program->toArray());

        return Redirect::to('master/seasonal-program')->withSuccess('A new seasonal program successfully created');
    }

    public function edit(Request $request)
    {
        if ($request->ajax()) {
            $seasonal_program_id = $request->route('seasonal_program');
            $seasonal_program = $this->seasonalProgramRepository->getSeasonalProgramById($seasonal_program_id);

            return response()->json($seasonal_program);
        }
    }

    public function update(StoreSeasonalProgramRequest $request, UpdateSeasonalProgramAction $updateSeasonalProgramAction, LogService $log_service)
    {
        $seasonal_program_id = $request->route('seasonal_program');

        $new_seasonal_program_details = $request->only([
            'prog_id',
            'start',
            'end',
            'sales_date'
        ]);

        $old_seasonal_program = $this->seasonalProgramRepository->getSeasonalProgramById($seasonal_program_id);

        DB::beginTransaction();
        try {
            
            $updated_seasonal_program = $updateSeasonalProgramAction->execute($seasonal_program_id, $new_seasonal_program_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_SEASONAL_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $new_seasonal_program_details);

            return Redirect::to('master/seasonal-program')->withError('Failed to update the seasonal program');

        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_SEASONAL_PROGRAM, 'Seasonal program has been updated', $updated_seasonal_program->toArray());

        return Redirect::to('master/seasonal-program')->withSuccess('The seasonal program successfully updated');
    }

    public function destroy(Request $request, DeleteSeasonalProgramAction $deleteSeasonalProgramAction, LogService $log_service)
    {
        $seasonal_program_id = $request->route('seasonal_program');

        $seasonal_program = $this->seasonalProgramRepository->getSeasonalProgramById($seasonal_program_id);

        DB::beginTransaction();
        try {

            $deleteSeasonalProgramAction->execute($seasonal_program_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_SEASONAL_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $seasonal_program->toArray());

            return Redirect::to('master/seasonal-program')->withError('Failed to delete the seasonal program');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_SEASONAL_PROGRAM, 'Seasonal program has been deleted', $seasonal_program->toArray());

        return Redirect::to('master/seasonal-program')->withSuccess('Seasonal program successfully deleted');
    }
}

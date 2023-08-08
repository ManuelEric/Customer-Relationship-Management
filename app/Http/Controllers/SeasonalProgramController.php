<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSeasonalProgramRequest;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SeasonalProgramRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SeasonalProgramController extends Controller
{
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

    public function store(StoreSeasonalProgramRequest $request)
    {
        $seasonalProgramDetails = $request->only([
            'prog_id',
            'start',
            'end',
            'sales_date'
        ]);

        DB::beginTransaction();
        try {
            
            $this->seasonalProgramRepository->storeSeasonalProgram($seasonalProgramDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to store seasonal program '.$e->getMessage().' | Line '.$e->getLine());
            return Redirect::to('master/seasonal-program')->withError('Failed to create a new seasonal program');

        }

        return Redirect::to('master/seasonal-program')->withSuccess('A new seasonal program successfully created');
    }

    public function edit(Request $request)
    {
        if ($request->ajax()) {
            $seasonalProgramId = $request->route('seasonal_program');
            $seasonalProgram = $this->seasonalProgramRepository->getSeasonalProgramById($seasonalProgramId);

            return response()->json($seasonalProgram);
        }
    }

    public function update(StoreSeasonalProgramRequest $request)
    {
        $seasonalProgramId = $request->route('seasonal_program');

        $newDetails = $request->only([
            'prog_id',
            'start',
            'end',
            'sales_date'
        ]);

        DB::beginTransaction();
        try {
            
            $this->seasonalProgramRepository->updateSeasonalProgram($seasonalProgramId, $newDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to update seasonal program '.$e->getMessage().' | Line '.$e->getLine());
            return Redirect::to('master/seasonal-program')->withError('Failed to update the seasonal program');

        }

        return Redirect::to('master/seasonal-program')->withSuccess('The seasonal program successfully updated');
    }

    public function destroy(Request $request)
    {
        $seasonalProgramId = $request->route('seasonal_program');

        DB::beginTransaction();
        try {

            $this->seasonalProgramRepository->deleteSeasonalProgram($seasonalProgramId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to delete seasonal program : ' . $e->getMessage().' | Line '.$e->getLine());
            return Redirect::to('master/seasonal-program')->withError('Failed to delete the seasonal program');
        }

        return Redirect::to('master/seasonal-program')->withSuccess('Seasonal program successfully deleted');
    }
}

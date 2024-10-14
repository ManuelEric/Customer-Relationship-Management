<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSeasonalProgramRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SeasonalProgramRepositoryInterface;
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

    public function store(StoreSeasonalProgramRequest $request)
    {
        $seasonal_program_details = $request->only([
            'prog_id',
            'start',
            'end',
            'sales_date'
        ]);

        DB::beginTransaction();
        try {
            
            $new_seasonal_program = $this->seasonalProgramRepository->storeSeasonalProgram($seasonal_program_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to store seasonal program '.$e->getMessage().' | Line '.$e->getLine());
            return Redirect::to('master/seasonal-program')->withError('Failed to create a new seasonal program');

        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Seasonal Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $new_seasonal_program);

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

    public function update(StoreSeasonalProgramRequest $request)
    {
        $seasonal_program_id = $request->route('seasonal_program');

        $new_details = $request->only([
            'prog_id',
            'start',
            'end',
            'sales_date'
        ]);

        $old_seasonal_program = $this->seasonalProgramRepository->getSeasonalProgramById($seasonal_program_id);

        DB::beginTransaction();
        try {
            
            $this->seasonalProgramRepository->updateSeasonalProgram($seasonal_program_id, $new_details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to update seasonal program '.$e->getMessage().' | Line '.$e->getLine());
            return Redirect::to('master/seasonal-program')->withError('Failed to update the seasonal program');

        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Seasonal Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $new_details, $old_seasonal_program);

        return Redirect::to('master/seasonal-program')->withSuccess('The seasonal program successfully updated');
    }

    public function destroy(Request $request)
    {
        $seasonal_program_id = $request->route('seasonal_program');

        $seasonal_program = $this->seasonalProgramRepository->getSeasonalProgramById($seasonal_program_id);

        DB::beginTransaction();
        try {

            $this->seasonalProgramRepository->deleteSeasonalProgram($seasonal_program_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to delete seasonal program : ' . $e->getMessage().' | Line '.$e->getLine());
            return Redirect::to('master/seasonal-program')->withError('Failed to delete the seasonal program');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Seasonal Program', Auth::user()->first_name . ' '. Auth::user()->last_name, $seasonal_program);

        return Redirect::to('master/seasonal-program')->withSuccess('Seasonal program successfully deleted');
    }
}

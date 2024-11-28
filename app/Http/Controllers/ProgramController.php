<?php

namespace App\Http\Controllers;

use App\Actions\Programs\CreateProgramAction;
use App\Actions\Programs\DeleteProgramAction;
use App\Actions\Programs\UpdateProgramAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreProgramRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\MainProgRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SubProgRepositoryInterface;
use App\Models\MainProg;
use App\Models\SubProg;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class ProgramController extends Controller
{
    use LoggingTrait;

    protected ProgramRepositoryInterface $programRepository;
    protected MainProgRepositoryInterface $mainProgRepository;
    protected SubProgRepositoryInterface $subProgRepository;

    public function __construct(ProgramRepositoryInterface $programRepository, MainProgRepositoryInterface $mainProgRepository, SubProgRepositoryInterface $subProgRepository)
    {
        $this->programRepository = $programRepository;
        $this->mainProgRepository = $mainProgRepository;
        $this->subProgRepository = $subProgRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->programRepository->getAllProgramsDataTables();
        }

        return view('pages.master.program.index');
    }

    public function store(StoreProgramRequest $request, CreateProgramAction $createProgramAction, LogService $log_service)
    {
        $new_program_details = $request->safe()->only([
            'prog_id',
            'prog_main',
            'prog_name',
            'prog_type',
            'prog_mentor',
            'prog_payment',
            'prog_scope',
            'active',
        ]);

        DB::beginTransaction();
        try {

            $new_program = $createProgramAction->execute($new_program_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $new_program_details);

            return Redirect::to('master/program')->withError('Failed to create a new program');
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_PROGRAM, 'New program has been added', $new_program->toArray());

        return Redirect::to('master/program')->withSuccess('Program successfully created');
    }

    public function create()
    {
        return view('pages.master.program.form')->with(
            [
                'main_programs' => $this->mainProgRepository->rnGetAllMainProg()
            ]
        );
    }

    public function update(StoreProgramRequest $request, UpdateProgramAction $updateProgramAction, LogService $log_service)
    {
        $new_program_details = $request->safe()->only([
            'old_prog_id',
            'prog_id',
            'prog_main',
            'prog_sub',
            'prog_name',
            'prog_type',
            'prog_mentor',
            'prog_payment',
            'prog_scope',
            'active',
        ]);
        
        DB::beginTransaction();
        try {
            
            $updated_program = $updateProgramAction->execute($request->old_prog_id, $new_program_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $new_program_details);

            return Redirect::to('master/program')->withError('Failed to update a program');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_PROGRAM, 'Program has been updated', $updated_program->toArray());

        return Redirect::to('master/program')->withSuccess('Program successfully updated');
    }

    public function edit(Request $request)
    {
        $prog_id = $request->route('program');
        $program = $this->programRepository->getProgramById($prog_id);

        return view('pages.master.program.form')->with(
            [
                'main_programs' => $this->mainProgRepository->rnGetAllMainProg(),
                'program' => $program
            ]
        );
    }

    public function destroy(Request $request, DeleteProgramAction $deleteProgramAction, LogService $log_service)
    {
        $program_id = $request->route('program');

        $old_program = $this->programRepository->getProgramById($program_id);

        DB::beginTransaction();
        try {

            $deleteProgramAction->execute($program_id);
            DB::commit();
        } catch (Exception $e) {
            
            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_PROGRAM, $e->getMessage(), $e->getLine(), $e->getFile(), $old_program->toArray());
            return Redirect::to('master/program')->withError('Failed to delete a program');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_PROGRAM, 'Program has been deleted', $old_program->toArray());

        return Redirect::to('master/program')->withSuccess('Program successfully deleted');
    }

    #
    public function fnGetSubProgram(Request $request)
    {
        $main_prog = $request->route('main_program');
        return json_encode($this->subProgRepository->getSubProgByMainProgId($main_prog));
    }
}

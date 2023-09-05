<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgramRequest;
use App\Interfaces\MainProgRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SubProgRepositoryInterface;
use App\Models\MainProg;
use App\Models\SubProg;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class ProgramController extends Controller
{

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

    public function store(StoreProgramRequest $request)
    {
        $programDetails = $request->only([
            'prog_id',
            'prog_main',
            'prog_name',
            'prog_type',
            'prog_mentor',
            'prog_payment',
            'prog_scope',
            'active',
        ]);

        # prog sub can be null
        if (isset($request->prog_sub))
            $programDetails['prog_sub'] = $request->prog_sub;

        DB::beginTransaction();
        try {

            $this->programRepository->createProgram($programDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create program failed : ' . $e->getMessage());
            return Redirect::to('master/program')->withError('Failed to create a new program');
        }

        return Redirect::to('master/program')->withSuccess('Program successfully created');
    }

    public function create()
    {
        return view('pages.master.program.form')->with(
            [
                'main_programs' => $this->mainProgRepository->getAllMainProg()
            ]
        );
    }

    public function update(StoreProgramRequest $request)
    {
        $programDetails = $request->only([
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

            $this->programRepository->updateProgram($request->old_prog_id, $programDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create program failed : ' . $e->getMessage());
            return Redirect::to('master/program')->withError('Failed to update a program');
        }

        return Redirect::to('master/program')->withSuccess('Program successfully updated');
    }

    public function edit(Request $request)
    {
        $prog_id = $request->route('program');
        $program = $this->programRepository->getProgramById($prog_id);

        return view('pages.master.program.form')->with(
            [
                'main_programs' => $this->mainProgRepository->getAllMainProg(),
                'program' => $program
            ]
        );
    }

    public function destroy(Request $request)
    {
        $id = $request->route('program');

        DB::beginTransaction();
        try {

            $this->programRepository->deleteProgram($id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete program failed : ' . $e->getMessage());
            return Redirect::to('master/program')->withError('Failed to delete a program');
        }

        return Redirect::to('master/program')->withSuccess('Program successfully deleted');
    }

    #
    public function getSubProgram(Request $request)
    {
        $mainProg = $request->route('main_program');
        return json_encode($this->subProgRepository->getSubProgByMainProgId($mainProg));
    }
}

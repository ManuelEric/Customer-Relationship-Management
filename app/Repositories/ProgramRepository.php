<?php

namespace App\Repositories;

use App\Interfaces\MainProgRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SubProgRepositoryInterface;
use App\Models\ClientProgram;
use App\Models\Program;
use App\Models\v1\Program as CRMProgram;
use App\Models\ViewProgram;
use DataTables;
use Illuminate\Support\Facades\DB;

class ProgramRepository implements ProgramRepositoryInterface
{

    protected MainProgRepositoryInterface $mainProgRepository;
    protected SubProgRepositoryInterface $subProgRepository;

    public function __construct(MainProgRepositoryInterface $mainProgRepository, SubProgRepositoryInterface $subProgRepository)
    {
        $this->mainProgRepository = $mainProgRepository;
        $this->subProgRepository = $subProgRepository;
    }

    public function getAllProgramsDataTables()
    {
        // $query = Program::leftJoin('tbl_main_prog', 'tbl_main_prog.id', '=', 'tbl_prog.main_prog_id')->select([
        //     'tbl_prog.*'
        // ]);
        return Datatables::eloquent(ViewProgram::query())->make(true);
    }

    public function getAllPrograms()
    {
        return Program::all();
    }

    public function getAllProgramByType($type, $active = null)
    {
        return Program::
            where('prog_type', $type)->when($active, function ($query) use ($active) {
                $query->where('active', $active);
            })->get();
    }

    public function getProgramById($programId)
    {
        return Program::whereProgId($programId);
    }

    public function getProgramByName($programName)
    {
        return Program::where('prog_program', $programName)->first();
    }

    public function deleteProgram($programId)
    {
        return Program::destroy($programId);
    }

    public function createProgram(array $programDetails)
    {
        if (array_key_exists('prog_name', $programDetails)) {
            $programDetails['prog_program'] = $programDetails['prog_name'];
            unset($programDetails['prog_name']);
        }

        if (!array_key_exists('main_prog_id', $programDetails)) {
            $programDetails['main_prog_id'] = $programDetails['prog_main'];
            unset($programDetails['prog_main']);
        }

        if (!array_key_exists('sub_prog_id', $programDetails) && array_key_exists('prog_sub', $programDetails)) {
            $programDetails['sub_prog_id'] = $programDetails['prog_sub'];
            unset($programDetails['prog_sub']);
        }

        $mainProg = $this->mainProgRepository->getMainProgById($programDetails['main_prog_id']);

        if (isset($programDetails['prog_sub'])) {
            $programDetails['sub_prog_id'] = $programDetails['prog_sub'];
            unset($programDetails['prog_sub']);

            $subProg = $this->subProgRepository->getSubProgById($programDetails['sub_prog_id']);
            $programDetails['prog_sub'] = $subProg->sub_prog_name;
        }

        # fetch prog name & sub prog name
        $programDetails['prog_main'] = $mainProg->prog_name;

        # disesuaikan dengan main_prog_id & sub_prog_id
        return Program::create($programDetails);
    }

    public function createProgramFromV1(array $programDetails)
    {
        return Program::create($programDetails);
    }

    public function updateProgram($programId, array $newDetails)
    {
        # initialize
        $newDetails['prog_program'] = $newDetails['prog_name'];
        unset($newDetails['prog_name']);

        $newDetails['main_prog_id'] = $newDetails['prog_main'];
        unset($newDetails['prog_main']);

        $mainProg = $this->mainProgRepository->getMainProgById($newDetails['main_prog_id']);

        if (isset($newDetails['prog_sub'])) {
            $newDetails['sub_prog_id'] = $newDetails['prog_sub'];
            unset($newDetails['prog_sub']);

            $subProg = $this->subProgRepository->getSubProgById($newDetails['sub_prog_id']);
            $newDetails['prog_sub'] = $subProg->sub_prog_name;
        }

        # fetch prog name & sub prog name
        $newDetails['prog_main'] = $mainProg->prog_name;

        # disesuaikan dengan main_prog_id & sub_prog_id
        return Program::whereProgId($programId)->update($newDetails);
    }

    public function cleaningProgram()
    {
        $programs = Program::all();
        foreach ($programs as $program) {
            # fetch the detail
            $detail = Program::where('prog_id', $program->prog_id)->first();

            # initialize
            if ($main_prog = $this->mainProgRepository->getMainProgByName($program->prog_main)) {

                $main_prog_id = $main_prog->id;
                $detail->main_prog_id = $main_prog_id;
            }

            if ($sub_prog = $this->subProgRepository->getSubProgBySubProgName($program->prog_sub)) {

                $sub_prog_id = $sub_prog->id;
                $detail->sub_prog_id = $sub_prog_id;
            }

            # update
            $detail->save();
        }
    }

    # CRM
    public function getProgramFromV1()
    {
        $crmprograms = CRMProgram::select([
            'prog_id',
            'main_number',
            DB::raw('(CASE 
                WHEN prog_main = "" THEN NULL ELSE prog_main
            END) as prog_main'),
            DB::raw('(CASE 
                WHEN prog_sub = "" THEN NULL ELSE prog_sub
            END) as prog_sub'),
            'prog_program',
            'prog_type',
            'prog_mentor',
            'prog_payment'
        ])->get();

        foreach ($crmprograms as $program) {
            
            $main_prog = $this->mainProgRepository->getMainProgByName($program->prog_main);
            $sub_prog = $this->subProgRepository->getSubProgBySubProgName($program->prog_sub);

            $response[] = [
                'prog_id' => $program->prog_id,
                'main_prog_id' => $main_prog->id ?? null,
                'prog_main' => $program->prog_main,
                'main_number' => $program->main_number,
                'sub_prog_id' => $sub_prog->id ?? null,
                'prog_sub' => $program->prog_sub,
                'prog_program' => $program->prog_program,
                'prog_type' => $program->prog_type,
                'prog_mentor' => $program->prog_mentor,
                'prog_payment' => $program->prog_payment
            ];
        }

        return $response;
    }
}

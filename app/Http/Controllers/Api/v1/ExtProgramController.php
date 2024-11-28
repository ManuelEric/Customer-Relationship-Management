<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\MainProgRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use Illuminate\Http\Request;

class ExtProgramController extends Controller
{
    protected ProgramRepositoryInterface $programRepository;
    protected MainProgRepositoryInterface $mainProgRepository;

    public function __construct(ProgramRepositoryInterface $programRepository, MainProgRepositoryInterface $mainProgRepository)
    {
        $this->programRepository = $programRepository;
        $this->mainProgRepository = $mainProgRepository;
    }

    public function getPrograms(Request $request)
    {
        $programs = $this->programRepository->getAllPrograms();
        if (!$programs) {
            return response()->json([
                'success' => true,
                'message' => 'No programs were found.'
            ]);
        }

        # map the data that being shown to the user
        $mapped_programs = $programs->map(function ($value) {

            return [
                'prog_id' => $value->prog_id,
                'program_name' => $value->program_name,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'There are programs found.',
            'data' => $mapped_programs
        ]);
    }

    public function getProgramsByMainProg(Request $request)
    {
        $requested_main_program = $request->route('main_program');
        $founded_main_program = $this->mainProgRepository->getMainProgByName($requested_main_program);
        if (!$founded_main_program) {
            return response()->json([
                'success' => false,
                'message' => 'Main program not valid.'
            ]);
        }

        $main_prog_id = $founded_main_program->id;

        # get the program by main program id
        $programs = $this->programRepository->getProgramsByMainProg($main_prog_id);
        if (!$programs) {
            return response()->json([
                'success' => true,
                'message' => 'No programs were found.',
            ]);
        }

        # map the data that being shown to the user
        $mapped_programs = $programs->map(function ($value) {
            return [
                'prog_id' => $value->prog_id,
                'program_name' => $value->program_name,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'There are programs found.',
            'data' => $mapped_programs
        ]);

    }

    public function getProgramsByType(Request $request)
    {
        $requested_type = strtolower($request->route("type"));
        if (!in_array($requested_type, ['b2b', 'b2c'])) {
            return response()->json([
                'success' => false,
                'message' => 'Type is not valid.'
            ]);
        }

        $programs = $this->programRepository->getAllProgramByType($requested_type);
        if (!$programs) {
            return response()->json([
                'success' => true,
                'message' => 'No programs were found.',
            ]);
        
        }

        # map the data that being shown to the user
        $mapped_programs = $programs->map(function ($value) {
            return [
                'prog_id' => $value->prog_id,
                'program_name' => $value->program_name,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'There are programs found.',
            'data' => $mapped_programs
        ]);
    }
}

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
        $mappedPrograms = $programs->map(function ($value) {

            return [
                'prog_id' => $value->prog_id,
                'program_name' => $value->program_name,
                'formatted' => $value->program_name.' | '.$value->prog_id
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'There are programs found.',
            'data' => $mappedPrograms
        ]);
    }

    public function getProgramsByMainProg(Request $request)
    {
        $requestedMainProgram = $request->route('main_program');
        $foundedMainProgram = $this->mainProgRepository->getMainProgByName($requestedMainProgram);
        if (!$foundedMainProgram) {
            return response()->json([
                'success' => false,
                'message' => 'Main program not valid.'
            ]);
        }

        $mainProgId = $foundedMainProgram->id;

        # get the program by main program id
        $programs = $this->programRepository->getProgramsByMainProg($mainProgId);
        if (!$programs) {
            return response()->json([
                'success' => true,
                'message' => 'No programs were found.',
            ]);
        }

        # map the data that being shown to the user
        $mappedPrograms = $programs->map(function ($value) {
            return [
                'prog_id' => $value->prog_id,
                'program_name' => $value->program_name,
                'formatted' => $value->program_name.' | '.$value->prog_id
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'There are programs found.',
            'data' => $mappedPrograms
        ]);

    }
}

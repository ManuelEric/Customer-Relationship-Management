<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\ProgramRepositoryInterface;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    protected ProgramRepositoryInterface $programRepository;

    public function __construct(ProgramRepositoryInterface $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    public function getProgramNameByMainProgramId(Request $request)
    {
        $main_prog_id = $request->route('mainProgId');

        $programs = $this->programRepository->getProgramNameByMainProgId($main_prog_id);

        return response()->json([
            'success' => true,
            'data' => $programs
        ]);
    }
}

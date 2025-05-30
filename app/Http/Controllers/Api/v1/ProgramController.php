<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\ProgramRepositoryInterface;
use App\Models\MainProg;
use App\Models\Program;
use App\Models\SubProg;
use Exception;
use Illuminate\Http\JsonResponse;
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

    public function getSubProgramNameByMainProgramId(MainProg $main_prog): JsonResponse 
    {
        try {

            // only show sub program active
            return response()->json($main_prog->subProgram()->where('sub_prog_status', 1)->get());
        } catch (Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 500);
        }
    }

    public function getProgramNameByMainAndSubProgramId(MainProg $main_prog, SubProg $sub_prog)
    {
        try {

            // only show program active
            $result = $main_prog->program()->when($sub_prog->id, function ($query) use ($sub_prog) {
                $query->where('sub_prog_id', $sub_prog->id);
            })->where('active', 1)->get()->makeHidden(['created_at', 'updated_at']);

            return response()->json($result);
        } catch (Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 500);
        }
    }
}

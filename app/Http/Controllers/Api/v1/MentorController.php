<?php

namespace App\Http\Controllers\Api\v1;

use App\Enum\LogModule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StoreMentorEducationRequest;
use App\Models\User;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MentorController extends Controller
{
    public function fnGetEducation(Request $request)
    {
        $educations = $request->user()->educations;
        $mapped_educations = $educations->map(function($item) {
            return [
                'university' => $item->univ_name,
                'major' => $item->major_name,
                'degree' => $item->pivot->degree,
                'graduated_at' => $item->pivot->graduation_date
            ];
        });

        return response()->json($mapped_educations);
    }

    public function fnStoreEducation(
        StoreMentorEducationRequest $request,
        LogService $log_service
        )
    {
        //! not finished
        $validated = $request->safe()->only([
            'degree',
            'univ_id',
            'other_univ_name',
            'major_id',
            'other_major_name',
            'graduation_date'
        ]);

        DB::beginTransaction();
        try {
            $user = User::find($request->user()->id);
            $user->educations()->attach($validated);
            DB::commit();
            $log_service->createSuccessLog(LogModule::ADD_EDUCATION_INFO, 'Add education info', $validated);
            return response()->json([
                'message' => 'Education info added successfully',
                'data' => $validated
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();
            $log_service->createErrorLog(LogModule::ADD_EDUCATION_INFO, $e->getMessage(), $e->getLine(), $e->getFile(), $validated);
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Failed to add education info',
                    'error' => $e->getMessage()
                ], JsonResponse::HTTP_BAD_REQUEST)
            );
        }
    }
}

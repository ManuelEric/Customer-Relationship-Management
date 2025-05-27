<?php

namespace App\Http\Controllers\Api\v1;

use App\Actions\Universities\CreateUniversityAction;
use App\Enum\LogModule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StoreMentorEducationRequest;
use App\Http\Resources\Mentor\MentorEducationCollectionResource;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Models\User;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MentorController extends Controller
{
    protected UniversityRepositoryInterface $universityRepository;
    protected MajorRepositoryInterface $majorRepository;

    public function __construct(UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository)
    {
        $this->universityRepository = $universityRepository;
        $this->majorRepository = $majorRepository;
    }

    public function fnGetEducation(Request $request)
    {
        $educations = $request->user()->educations;
        return new MentorEducationCollectionResource($educations);
    }

    public function fnStoreEducation(
        StoreMentorEducationRequest $request,
        LogService $log_service,
        CreateUniversityAction $createUniversityAction,
        )
    {
        $validated = $request->safe()->only([
            'degree',
            'univ_id',
            'other_univ_name',
            'major_id',
            'other_major_name',
            'graduation_date'
        ]);

        $education_info_details = [
            'degree' => $validated['degree'],
            'univ_id' => $validated['univ_id'] ?? null,
            'major_id' => $validated['major_id'] ?? null,
            'graduation_date' => $validated['graduation_date']
        ];

        DB::beginTransaction();
        try {

            $university = $validated['univ_id'] ? $this->universityRepository->getUniversityById($validated['univ_id']) : null;
            $education_info_details['univ_id'] = $university ? $university->univ_id : null;
            # if other univ name selected
            # we have to store the other univ_name into tbl_univ
            if ($validated['other_univ_name']) {
                $existing = $this->universityRepository->getUniversityByName($validated['other_univ_name']);
                $education_info_details['univ_id'] = $existing ? $existing->univ_id : $createUniversityAction->execute(['univ_name' => $validated['other_univ_name']])->univ_id;
            }

            $major = $validated['major_id'] ? $this->majorRepository->getMajorById($validated['major_id']) : null;
            $education_info_details['major_id'] = $major ? $major->id : null;
            # if other major name selected
            # we have to store the other major_name into tbl_major
            if ($validated['other_major_name']) {
                $existing = $this->majorRepository->getMajorByName($validated['other_major_name']);
                $education_info_details['major_id'] = $existing ? $existing->id : $this->majorRepository->createMajor(['name' => $validated['other_major_name']])->id;
            }

            
            $user = User::find($request->user()->id);
            if ( $user->educations()->wherePivot('univ_id', $education_info_details['univ_id'])->wherePivot('major_id', $education_info_details['major_id'])->exists() ) {
                return response()->json([
                    'message' => 'Education already exists'
                ], JsonResponse::HTTP_CONFLICT);
            }

            $user->educations()->attach($education_info_details['univ_id'], $education_info_details);
            DB::commit();
            $log_service->createSuccessLog(LogModule::ADD_EDUCATION_INFO, 'Add education info', $validated);
            return response()->json([
                'message' => 'Education info added successfully',
                'data' => new MentorEducationCollectionResource($user->educations)
            ], JsonResponse::HTTP_CREATED);
        } catch (Exception $err) {
            DB::rollBack();
            $log_service->createErrorLog(LogModule::ADD_EDUCATION_INFO, $err->getMessage(), $err->getLine(), $err->getFile(), $validated);
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Failed to add education info',
                    'error' => $err->getMessage()
                ], JsonResponse::HTTP_BAD_REQUEST)
            );
        }
    }

    public function fnDeleteEducation(
        $user_education_id,
        Request $request,
        LogService $log_service,
        )
    {
        $user = User::find($request->user()->id);
        $education = $user->educations()->wherePivot('tbl_user_educations.id', $user_education_id)->first();
        if (!$education) {
            return response()->json([
                'message' => 'Education not found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        DB::beginTransaction();
        try {
            // Detach only the record matching univ_id and major_id
            DB::table('tbl_user_educations')->where('id', $user_education_id)->delete();
            DB::commit();
            $log_service->createSuccessLog(LogModule::DELETE_EDUCATION_INFO, 'Add education info', $education->toArray());
            return response()->json([
                'message' => 'Education deleted successfully'
            ], JsonResponse::HTTP_OK);
        } catch (Exception $err) {
            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_EDUCATION_INFO, $err->getMessage(), $err->getLine(), $err->getFile(), $education->toArray());
            return response()->json([
                'message' => 'Failed to delete education',
                'error' => $err->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

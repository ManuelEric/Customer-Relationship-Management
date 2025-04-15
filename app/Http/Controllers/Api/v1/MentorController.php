<?php

namespace App\Http\Controllers\Api\v1;

use App\Enum\LogModule;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StoreMentorEducationRequest;
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

        $education_info_details = [
            'degree' => $validated['degree'],
            'graduation_date' => $validated['graduation_date']
        ];

        DB::beginTransaction();
        try {

            # if other univ name selected
            # we have to store the other univ_name into tbl_univ
            if ( ($validated['other_univ_name'] !== NULL) && (!$existing_university = $this->universityRepository->getUniversityByName($validated['other_univ_name'])) )
            {
                $new_university_details = [
                    'univ_name' => $validated['other_univ_name'],
                ];
                $university = $this->universityRepository->createUniversity($new_university_details);
            }


            # fill univ_id from university / existing university
            $education_info_details['univ_id'] = $validated['other_univ_name'] !== NULL ? $university->univ_id : $existing_university->univ_id;

            # if other major name selected
            # we have to store the other major_name into tbl_major
            if ( ($validated['other_major_name'] !== NULL) && (!$existing_major = $this->majorRepository->createMajor($validated['other_major_name'])) )
            {
                $new_major_details = [
                    'major_name' => $validated['other_major_name'],
                ];
                $major = $this->majorRepository->createMajor($new_major_details);
            }

            # fill major_id from major / existing major
            $education_info_details['major_id'] = $validated['other_major_name'] !== NULL ? $major->id : $existing_major->id;


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

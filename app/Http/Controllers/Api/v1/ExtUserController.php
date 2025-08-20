<?php

namespace App\Http\Controllers\Api\v1;

use App\Enum\LogModule;
use App\Http\Controllers\Controller;
use App\Interfaces\SubjectRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Models\UserClient;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExtUserController extends Controller
{
    protected UserRepositoryInterface $userRepository;

    protected SubjectRepositoryInterface $subjectRepository;

    public function __construct(UserRepositoryInterface $userRepository, SubjectRepositoryInterface $subjectRepository)
    {
        $this->userRepository = $userRepository;
        $this->subjectRepository = $subjectRepository;
    }

    // used for spreadsheets
    public function getMemberOfDepartments(Request $request)
    {
        $department = $request->route('department');
        if ($department === null) {
            return response()->json(['success' => false, 'message' => 'The requested data is not valid.']);
        }

        $decodedDepartment = urldecode($department);

        // only select the active users
        $usersFromDepartment = $this->userRepository->rnGetAllUsersByDepartmentAndRole('employee', $decodedDepartment);

        // when user not found
        if (! $usersFromDepartment) {
            return response()->json([
                'success' => true,
                'message' => 'No employee from '.$decodedDepartment.' department were found.',
            ]);
        }

        // map the data that being shown to the user
        $mappingTheData = $usersFromDepartment->map(function ($value) {
            $trimmedFullname = trim($value->full_name);

            return [
                'fullname' => $trimmedFullname,
                'id' => $value->id,
                'extended_id' => $value->extended_id,
                'formatted' => $trimmedFullname.' | '.$value->id,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'There are few members from a '.$decodedDepartment.' department.',
            'data' => $mappingTheData,
        ]);
    }

    public function getEmployees(Request $request)
    {
        $employees = $this->userRepository->rnGetAllUsersByRole('Employee');
        if (! $employees) {
            return response()->json([
                'success' => true,
                'message' => 'No employee were found.',
            ]);
        }

        // map the data that being shown to the user
        $mappingEmployees = $employees->map(function ($value) {

            $trimmedFullname = trim($value->full_name);

            return [
                'fullname' => $trimmedFullname,
                'id' => $value->id,
                'extended_id' => $value->extended_id,
                'formatted' => $trimmedFullname.' | '.$value->id,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Employee are found',
            'data' => $mappingEmployees,
        ]);
    }

    public function cnGetSubjectsByRole(Request $request)
    {
        $role = $request->route('role');
        $response = [];
        $http_code = null;

        if ($role == 'Associate Editor' || $role == 'Senior Editor' || $role == 'Managing Editor') {
            $role = 'Editor';
        }

        try {
            // Temporary change to get all subject
            // $subjects = $this->subjectRepository->rnGetAllSubjectsByRole($role);
            $subjects = $this->subjectRepository->getAllSubjects();

            if (! $subjects) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subject not found.',
                ], 503);
            }
        } catch (Exception $e) {
            Log::error('Failed get subject'.$e->getMessage());

            $response = [
                'success' => false,
                'message' => 'Failed get subject! '.$e->getMessage(),
            ];
            $http_code = 500;
        }

        $response = [
            'success' => true,
            'message' => 'There are subject found.',
            'data' => $subjects,
        ];
        $http_code = 200;

        return response()->json(
            $response, $http_code
        );
    }

    public function fnGetUserByUUID(
        Request $request,
        LogService $log_service,
    ) {
        $user_uuid = $request->route('UUID');

        try {
            $user = $this->userRepository->rnGetUserById($user_uuid);

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 503);
            }

            return response()->json([
                'success' => true,
                'message' => 'User found.',
                'data' => $user,
            ], JsonResponse::HTTP_OK);
        } catch (Exception $err) {
            $log_service->createErrorLog(LogModule::GET_USER, $err->getMessage(), $err->getLine(), $err->getFile());

            return response()->json([
                'success' => false,
                'message' => 'Failed get user '.$err->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fnGetMentors()
    {
        // get the active mentors
        $existingMentors = $this->userRepository->rnGetExistingMentorsAPI();
        if ($existingMentors->count() == 0) {
            return response()->json([
                'success' => true,
                'message' => 'No mentor found.',
            ]);
        }

        // map the data that being shown to the user
        $mappedExistingMentors = $existingMentors->map(function ($value) {
            $trimmedFullname = trim($value->full_name);

            return [
                /* essay editing purposes */
                'first_name' => $value->first_name,
                'last_name' => $value->last_name,
                'phone' => $value->phone,
                'email' => $value->email,
                'address' => $value->address,
                'roles' => $value->roles,
                'educations' => $value->educations,
                /* end */

                'fullname' => $trimmedFullname,
                'id' => $value->id,
                'extended_id' => $value->extended_id,
                'formatted' => $trimmedFullname.' | '.$value->id,
            ];
        });

        return response()->json(
            [
                'success' => true,
                'message' => 'Mentors data found.',
                'data' => $mappedExistingMentors,
            ]
        );
    }

    public function fnGetMentorsCapacity(Request $request)
    {
        $terms = $request->get('terms');
        $search = compact('terms');

        // get the active mentors
        $existing_mentors = $this->userRepository->rnGetExistingMentorsAPI($search);
        if ($existing_mentors->count() == 0) {
            return response()->json([
                'success' => true,
                'message' => 'No mentor found.',
            ]);
        }

        $mapped_existing_mentors = $existing_mentors->map(function ($value) {
            $load = $value->mentorClient()->wherePivot('tbl_client_mentor.type', 2)->wherePivot('tbl_client_mentor.status', 1)->successAndPaid()->get();

            $mentee_enrollment = UserClient::with([
                'universityAcceptance' => function ($query) {
                    $query->select('tbl_univ.univ_id', 'tbl_univ.univ_name');
                },
            ])->whereIn('id', $load->pluck('client_id')->toArray())->whereNotNull('application_year')->where('application_year', Carbon::now()->format('Y'))->get();
            $mapped_mentee_enrollment = $mentee_enrollment->map(function ($value) {
                return [
                    'id' => $value->id,
                    'first_name' => $value->first_name,
                    'last_name' => $value->last_name,
                ];
            });

            return [
                'uuid' => $value->id,
                'first_name' => $value->first_name,
                'last_name' => $value->last_name,
                'email' => $value->email,
                'capacity' => $value->roles->first()->pivot->capacity,
                'load' => count($load),
                'mentee_enrollment' => $mentee_enrollment->count(),
                'detail_mentee_enrollment' => $mapped_mentee_enrollment,
            ];
        });

        return $mapped_existing_mentors->paginate(10);
    }

    public function fnUpdateMentorCapacity(
        Request $request,
        User $mentor,
        LogService $log_service
    ) {
        $new_capacity = $request->capacity;

        DB::beginTransaction();
        try {
            $mentor_role = $mentor->roles()->where('role_name', 'Mentor')->first();
            $mentor_role->pivot->capacity = $new_capacity;
            $mentor_role->pivot->save();
            DB::commit();
        } catch (Exception $err) {
            DB::rollBack();
            $log_service->createErrorLog(LogModule::CHANGE_USER_CAPACITY, $err->getMessage(), $err->getLine(), $err->getFile(), $mentor->toArray());

            return response()->json(['message' => 'Failed to update mentor\'s capacity of '.$new_capacity], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $log_service->createSuccessLog(LogModule::CHANGE_USER_CAPACITY, 'The mentor\'s capacity has been updated', $mentor->toArray());

        return response()->json(['message' => 'The mentor\'s capacity has been updated.'], JsonResponse::HTTP_OK);
    }
}

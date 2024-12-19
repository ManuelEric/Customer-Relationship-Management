<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\SubjectRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
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

    # used for spreadsheets
    public function getMemberOfDepartments(Request $request)
    {
        $department = $request->route('department');
        if ($department === null)
            return response()->json(['success' => false, 'message' => 'The requested data is not valid.']);

        $decodedDepartment = urldecode($department);
        
        # only select the active users
        $usersFromDepartment = $this->userRepository->rnGetAllUsersByDepartmentAndRole('employee', $decodedDepartment);

        # when user not found
        if (!$usersFromDepartment) {
            return response()->json([
                'success' => true,
                'message' => 'No employee from '.$decodedDepartment.' department were found.'
            ]);
        }

        # map the data that being shown to the user
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
            'data' => $mappingTheData
        ]);
    }

    
    public function getEmployees(Request $request)
    {
        $employees = $this->userRepository->rnGetAllUsersByRole('Employee');
        if (!$employees) {
            return response()->json([
                'success' => true,
                'message' => 'No employee were found.'
            ]);
        }

        # map the data that being shown to the user
        $mappingEmployees = $employees->map(function($value) {
            
            $trimmedFullname = trim($value->full_name);

            return [
                'fullname' => $trimmedFullname,
                'id' => $value->id,
                'extended_id' => $value->extended_id,
                'formatted' => $trimmedFullname.' | '. $value->id
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Employee are found',
            'data' => $mappingEmployees
        ]);
    }

    public function cnGetSubjectsByRole(Request $request)
    {
        $role = $request->route('role');
        $response = [];
        $http_code = null;
        
        if($role == 'Associate Editor' || $role == 'Senior Editor' || $role == 'Managing Editor'){
            $role = 'Editor';
        } 

        try {
            $subjects = $this->subjectRepository->rnGetAllSubjectsByRole($role);
            
            if (!$subjects) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subject not found.'
                ], 503);
            }
        } catch (Exception $e) {
            Log::error('Failed get subject' . $e->getMessage());

            $response = [
                'success' => false,
                'message' => 'Failed get subject! '. $e->getMessage(), 
            ];
            $http_code = 500;
        }

        $response = [
            'success' => true,
            'message' => 'There are subject found.',
            'data' => $subjects
        ];
        $http_code = 200;

        return response()->json(
            $response, $http_code
        );
    }
}

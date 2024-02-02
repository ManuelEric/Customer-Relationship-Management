<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

class ExtUserController extends Controller
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    # used for spreadsheets
    public function getMemberOfDepartments(Request $request)
    {
        $department = $request->route('department');
        if ($department === null)
            return response()->json(['success' => false, 'message' => 'The requested data is not valid.']);

        $decodedDepartment = urldecode($department);
        
        # only select the active users
        $usersFromDepartment = $this->userRepository->getAllUsersByDepartmentAndRole('employee', $decodedDepartment);

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
        $employees = $this->userRepository->getAllUsersByRole('Employee');
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
}

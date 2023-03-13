<?php

namespace App\Http\Controllers;

use App\Interfaces\DepartmentRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    private DepartmentRepositoryInterface $departmentRepository;

    public function __construct(DepartmentRepositoryInterface $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    public function getEmployeeByDepartment(Request $request)
    {
        $departmentId = $request->route('department');
        try {

            $html = '';
            $employees = $this->departmentRepository->getEmployeeByDepartment($departmentId);
            if ($employees->count() > 0) {
                foreach ($employees as $employee) {
    
                    $html .= '<li class="list-group-item d-flex justify-content-between cursor-pointer" id="'.$employee->uuid.'"
                                    onclick="checkUser(\''.$employee->uuid.'\')">'.$employee->full_name.'<i class="bi bi-arrow-right"></i>
                                </li>';
                }
            } else {
                $html .= '<li class="list-group-item d-flex justify-content-between cursor-pointer">No Employee</li>';
            }
            
            
        } catch (Exception $e) {
            
            Log::error('Failed to get employee by department : '.$e->getMessage());
            return response()->json([
                'message' => 'There was an error getting employee by department'
            ], 500);

        }

        return response()->json([
            'html_cxt' => $html
        ]);
    }
}

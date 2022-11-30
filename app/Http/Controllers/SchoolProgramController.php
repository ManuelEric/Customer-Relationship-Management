<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolProgramRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolProgramController extends Controller
{
    use CreateCustomPrimaryKeyTrait;

    protected SchoolRepositoryInterface $schoolRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected UserRepositoryInterface $userRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, UserRepositoryInterface $userRepository, SchoolProgramRepositoryInterface $schoolProgramRepository, ProgramRepositoryInterface $programRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->programRepository = $programRepository;
        $this->userRepository = $userRepository;
    }


    public function store(StoreSchoolProgramRequest $request)
    {
    
        $schoolId = $request->route('school');

        $schoolPrograms = $request->all();
        
        DB::beginTransaction();
        $schoolPrograms['created_at'] = Carbon::now();
        $schoolPrograms['updated_at'] = Carbon::now();
       
        try {

            # insert into school program
            $this->schoolProgramRepository->createSchoolProgram($schoolPrograms);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store school failed : ' . $e->getMessage());
            return Redirect::to('program/school/'. $schoolId .'/detail/create')->withError('Failed to create school program' . $e->getMessage());
        }

        return Redirect::to('program/school/'. $schoolId .'/detail/create')->withSuccess('School program successfully created');
    }

    public function create(Request $request)
    {
        $schoolId = $request->route('school');

         # retrieve school data by id
         $school = $this->schoolRepository->getSchoolById($schoolId);

         # retrieve program data
         $programs = $this->programRepository->getAllPrograms();

         # retrieve employee data
         $employees = $this->userRepository->getAllUsersByRole('Employee');
 
        return view('pages.program.school-program.form')->with(
            [
                'employees' => $employees,
                'programs' => $programs,
                'school' => $school
            ]
        );
    }
    
}
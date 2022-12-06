<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolProgramRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\SchoolProgramAttachRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\SchoolDetailRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\ReasonRepositoryInterface;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\CorporatePicRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\UniversityPicRepositoryInterface;
use App\Models\Reason;
use App\Models\SchoolProgram;
use Exception;
use Illuminate\Database\Eloquent\Collection;
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
    protected SchoolProgramAttachRepositoryInterface $schoolProgramAttachRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected UserRepositoryInterface $userRepository;
    protected ReasonRepositoryInterface $rasonRepository;
    protected CorporateRepositoryInterface $corporateRepository;
    protected CorporatePicRepositoryInterface $corporatePicRepository;
    protected UniversityRepositoryInterface $universityRepository;
    protected UniversityPicRepositoryInterface $universityPicRepository;
    protected SchoolDetailRepositoryInterface $schoolDetailRepository;

    public function __construct(
        SchoolRepositoryInterface $schoolRepository, 
        UserRepositoryInterface $userRepository, 
        SchoolProgramRepositoryInterface $schoolProgramRepository, 
        SchoolProgramAttachRepositoryInterface $schoolProgramAttachRepository, 
        ProgramRepositoryInterface $programRepository,
        ReasonRepositoryInterface $reasonRepository,
        CorporateRepositoryInterface $corporateRepository,
        CorporatePicRepositoryInterface $corporatePicRepository,
        UniversityRepositoryInterface $universityRepository,
        UniversityPicRepositoryInterface $universityPicRepository,
        SchoolDetailRepositoryInterface $schoolDetailRepository,
        )
    {
        $this->schoolRepository = $schoolRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->schoolProgramAttachRepository = $schoolProgramAttachRepository;
        $this->programRepository = $programRepository;
        $this->userRepository = $userRepository;
        $this->reasonRepository = $reasonRepository;
        $this->corporateRepository = $corporateRepository;
        $this->corporatePicRepository = $corporatePicRepository;
        $this->universityRepository = $universityRepository;
        $this->universityPicRepository = $universityPicRepository;
        $this->schoolDetailRepository = $schoolDetailRepository;
    }


    public function store(StoreSchoolProgramRequest $request)
    {
    
        $schoolId = $request->route('school');

        $schoolPrograms = $request->all();
        if ($request->input('reason_id') == 'other'){
            $reason['reason_name'] = $request->input('other_reason');
        }

        DB::beginTransaction();
        $schoolPrograms['sch_id'] = $schoolId;
       
        try {
            # insert into reason
            if ($request->input('reason_id') == 'other'){
                $this->reasonRepository->createReason($reason);
                $reason_id = Reason::max('reason_id');
                $schoolPrograms['reason_id'] = $reason_id;
            }
            # insert into school program
            $this->schoolProgramRepository->createSchoolProgram($schoolPrograms);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store school failed : ' . $e->getMessage());
            return Redirect::to('program/school/'. $schoolId .'/detail/create')->withError('Failed to create school program');
        }
        
        # status == success
        if($schoolPrograms['status'] == 1) 
        {
            $sch_progId = SchoolProgram::max('id');
            return Redirect::to('program/school/'. $schoolId .'/detail/create')
            ->withSuccess('School program successfully created')
            ->with([
                'attach' => true,
                'sch_progId' => $sch_progId,
            ]);
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

         # retrieve reason data
         $reasons = $this->reasonRepository->getAllReasons();
         
         # retrieve employee data
         $employees = $this->userRepository->getAllUsersByRole('Employee');
 
        return view('pages.program.school-program.form')->with(
            [
                'employees' => $employees,
                'programs' => $programs,
                'reasons' => $reasons,
                'school' => $school
            ]
        );
    }

    public function show(Request $request)
    {
        $schoolId = $request->route('school');
        $sch_progId = $request->route('detail');

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($schoolId);
        
        # retrieve all school data
        $schools = $this->schoolRepository->getAllSchools();

        # retrieve all school detail by school id
        $schoolDetail = $this->schoolDetailRepository->getAllSchoolDetailsById($schoolId);
        
        # retrieve program data
        $programsB2B = $this->programRepository->getAllProgramByType('B2B');
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programs = $programsB2B->merge($programsB2BB2C);

        # retrieve reason data
        $reasons = $this->reasonRepository->getAllReasons();

        # retrieve School Program data by schoolId
        $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($sch_progId);
        
        # retrieve School Program Attach data by schoolId
        $schoolProgramAttachs = $this->schoolProgramAttachRepository->getAllSchoolProgramAttachsBySchprogId($sch_progId);
        
        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByRole('Employee');
 
        return view('pages.program.school-program.form')->with(
            [
                'employees' => $employees,
                'programs' => $programs,
                'reasons' => $reasons,
                'schoolProgram' => $schoolProgram,
                'schoolProgramAttachs' => $schoolProgramAttachs,
                'school' => $school,
                'schoolDetail' => $schoolDetail,
                'schools' => $schools,
                'attach' => true
            ]
        );
    }


    public function edit(Request $request)
   {
     
        // if ($request->ajax()) {
            $id = 'CORP-0001';

            $type = 'partner';
            if($type == 'partner'){
                return $this->corporatePicRepository->getAllCorporatePicByCorporateId($id);
            }
            return;
        // }

        $schoolId = $request->route('school');
        $sch_progId = $request->route('detail');

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($schoolId);

        # retrieve all school detail by school id
        $schoolDetail = $this->schoolDetailRepository->getAllSchoolDetailsById($schoolId);

        # retrieve program data
        $programsB2B = $this->programRepository->getAllProgramByType('B2B');
        $programsB2BB2C = $this->programRepository->getAllProgramByType('B2B/B2C');
        $programs = $programsB2B->merge($programsB2BB2C);

        

        # retrieve reason data
        $reasons = $this->reasonRepository->getAllReasons();

        # retrieve School Program data by id
        $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($sch_progId);
        
        # retrieve employee data
        $employees = $this->userRepository->getAllUsersByRole('Employee');

        # retrieve corporate / partner
        $partners = $this->corporateRepository->getAllCorporate();
        
        # retrieve university data
        $universities = $this->universityRepository->getAllUniversities();

        return view('pages.program.school-program.form')->with(
            [
                'edit' => true,
                'employees' => $employees,
                'programs' => $programs,
                'reasons' => $reasons,
                'schoolProgram' => $schoolProgram,
                'school' => $school,
                'schoolDetail' => $schoolDetail,
                'partners' => $partners,
                'universities' => $universities,
            ]
        );

   }
   
   public function update(Request $request){
        
        $schoolId = $request->route('school');
        $sch_progId = $request->route('detail');
        $schoolPrograms = $request->all();

        
        DB::beginTransaction();
        $schoolPrograms['sch_id'] = $schoolId;
        $schoolPrograms['updated_at'] = Carbon::now();
        try {
            
            # update reason school program
            if($schoolPrograms['status'] == 2){
                if($request->input('reason_id') == 'other'){
                    $reason['reason_name'] = $request->input('other_reason');
               
                        $this->reasonRepository->createReason($reason);
                        $reason_id = Reason::max('reason_id');
                        $schoolPrograms['reason_id'] = $reason_id;
                  
                }
            }
                
            # update school program
            $this->schoolProgramRepository->updateSchoolProgram($sch_progId, $schoolPrograms);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update school program failed : ' . $e->getMessage());
            return Redirect::to('program/school/' . $schoolId . '/detail/' . $sch_progId . '/edit')->withError('Failed to update school program'. $e->getMessage());
        }

        return Redirect::to('program/school/' . $schoolId . '/detail/' . $sch_progId)->withSuccess('School program successfully updated');
   }

   public function destroy(Request $request)
    {
        $schoolId = $request->route('school');
        $sch_progId = $request->route('detail');

        DB::beginTransaction();
        try {

            $this->schoolProgramRepository->deleteSchoolProgram($sch_progId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete school program failed : ' . $e->getMessage());
            return Redirect::to('program/school/' . $schoolId . '/detail/' . $sch_progId)->withError('Failed to delete school program');
        }

        return Redirect::to('instance/school/' . $schoolId)->withSuccess('School program successfully deleted');
    }
}
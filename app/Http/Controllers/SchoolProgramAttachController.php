<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolProgramAttachRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\SchoolProgramAttachRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class SchoolProgramAttachController extends Controller
{
    use CreateCustomPrimaryKeyTrait;

    protected SchoolRepositoryInterface $schoolRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected SchoolProgramAttachRepositoryInterface $schoolAttachProgramRepository;

    public function __construct(
        SchoolRepositoryInterface $schoolRepository, 
        SchoolProgramRepositoryInterface $schoolProgramRepository, 
        SchoolProgramAttachRepositoryInterface $schoolProgramAttachRepository, 
  
        )
    {
        $this->schoolRepository = $schoolRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->schoolProgramAttachRepository = $schoolProgramAttachRepository;

    }

    public function store(StoreSchoolProgramAttachRequest $request)
    {
        
    
        $schoolId = $request->route('school');
        $schoolProgramId = $request->route('sch_prog');
        
        $schProgAttachs = $request->all();
        $schProgAttachs['schprog_id'] = $schoolProgramId;

        $schprog_file = Str::slug($schProgAttachs['schprog_file'], "_").'_'.Str::slug(Carbon::now(),"_");
        $file = $request->file('schprog_attach');
        $extension = $file->getClientOriginalExtension();
        $file_location = 'attachment/sch_prog_attach/'.$schoolProgramId.'/'; 
        $schprog_attach = $file_location.$schprog_file.'.'.$extension;
        $file->move($file_location, $schprog_file.'.'.$extension);


        $schProgAttachs['schprog_file'] = $schprog_file;
        $schProgAttachs['schprog_attach'] = $schprog_attach;

        DB::beginTransaction();
       
        try {
           
            # insert into school program attachment
            $this->schoolProgramAttachRepository->createSchoolProgramAttach($schProgAttachs);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store school failed : ' . $e->getMessage());
            return Redirect::to('program/school/'. $schoolId .'/detail/create')->withError('Failed to create school program attachments'. $e->getMessage());
        }
        
       
        return Redirect::to('program/school/'. $schoolId .'/detail/'. $schoolProgramId)->withSuccess('School program attachments successfully created');
    }

    

   public function destroy(Request $request)
    {
        $schoolId = $request->route('school');
        $sch_progId = $request->route('sch_prog');
        $attachId = $request->route('attach');

        DB::beginTransaction();
        try {

            $schoolProgAttachs = $this->schoolProgramAttachRepository->getSchoolProgramAttachById($attachId);

            if($this->schoolProgramAttachRepository->deleteSchoolProgramAttach($attachId)){
                Unlink(public_path($schoolProgAttachs->schprog_attach));
            }
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete school program failed : ' . $e->getMessage());
            return Redirect::to('program/school/' . $schoolId . '/detail/' . $sch_progId)->withError('Failed to delete school program attachments'. $e->getMessage());
        }

        return Redirect::to('program/school/' . $schoolId . '/detail/' . $sch_progId)->withSuccess('School program attachments successfully deleted');
    }
}
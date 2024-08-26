<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolProgramAttachRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StoreAttachmentProgramTrait;
// use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\SchoolProgramAttachRepositoryInterface;
// use App\Interfaces\SchoolRepositoryInterface;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class SchoolProgramAttachController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use StoreAttachmentProgramTrait;

    // protected SchoolRepositoryInterface $schoolRepository;
    // protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected SchoolProgramAttachRepositoryInterface $schoolProgramAttachRepository;

    public function __construct(
        // SchoolRepositoryInterface $schoolRepository, 
        // SchoolProgramRepositoryInterface $schoolProgramRepository, 
        SchoolProgramAttachRepositoryInterface $schoolProgramAttachRepository,

    ) {
        // $this->schoolRepository = $schoolRepository;
        // $this->schoolProgramRepository = $schoolProgramRepository;
        $this->schoolProgramAttachRepository = $schoolProgramAttachRepository;
    }

    public function store(StoreSchoolProgramAttachRequest $request)
    {


        $schoolId = $request->route('school');
        $schoolProgramId = $request->route('sch_prog');

        $schProgAttachs = $request->all();
        $schProgAttachs['schprog_id'] = $schoolProgramId;

        $schprog_file =  $this->getFileNameAttachment($schProgAttachs['schprog_file']);

        $schprog_attach = $this->attachmentProgram($request->file('schprog_attach'), $schoolProgramId, $schprog_file);


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
            return Redirect::to('program/school/' . $schoolId . '/detail/create')->withError('Failed to create school program attachments' . $e->getMessage());
        }


        return Redirect::to('program/school/' . $schoolId . '/detail/' . $schoolProgramId)->withSuccess('School program attachments successfully created');
    }



    public function destroy(Request $request)
    {
        $schoolId = $request->route('school');
        $sch_progId = $request->route('sch_prog');
        $attachId = $request->route('attach');

        DB::beginTransaction();
        try {

            $schoolProgAttach = $this->schoolProgramAttachRepository->getSchoolProgramAttachById($attachId);
            if (File::exists(public_path($schoolProgAttach->schprog_attach))) {

                if ($this->schoolProgramAttachRepository->deleteSchoolProgramAttach($attachId)) {
                    Unlink(public_path($schoolProgAttach->schprog_attach));
                }
            } else {
                $this->schoolProgramAttachRepository->deleteSchoolProgramAttach($attachId);
            }


            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete school program attach failed : ' . $e->getMessage());
            return Redirect::to('program/school/' . $schoolId . '/detail/' . $sch_progId)->withError('Failed to delete school program attachments' . $e->getMessage());
        }

        return Redirect::to('program/school/' . $schoolId . '/detail/' . $sch_progId)->withSuccess('School program attachments successfully deleted');
    }
}

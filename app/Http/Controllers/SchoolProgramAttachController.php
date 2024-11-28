<?php

namespace App\Http\Controllers;

use App\Actions\SchoolPrograms\Attach\CreateSchoolProgramAttachAction;
use App\Actions\SchoolPrograms\Attach\DeleteSchoolProgramAttachAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreSchoolProgramAttachRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StoreAttachmentProgramTrait;
// use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\SchoolProgramAttachRepositoryInterface;
use App\Services\Log\LogService;
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

    public function store(StoreSchoolProgramAttachRequest $request, CreateSchoolProgramAttachAction $createSchoolProgramAttachAction, LogService $log_service)
    {


        $school_id = $request->route('school');
        $school_program_id = $request->route('sch_prog');

        DB::beginTransaction();

        try {

            # insert into school program attachment
            $created_partner_program_attach = $createSchoolProgramAttachAction->execute($request, $school_program_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_SCHOOL_PROGRAM_ATTACH, $e->getMessage(), $e->getLine(), $e->getFile(), $request->all());

            return Redirect::to('program/school/' . $school_id . '/detail/create')->withError('Failed to create school program attachments' . $e->getMessage());
        }

        $log_service->createSuccessLog(LogModule::STORE_SCHOOL_PROGRAM_ATTACH, 'New school program attach has been added', $created_partner_program_attach->toArray());

        return Redirect::to('program/school/' . $school_id . '/detail/' . $school_program_id)->withSuccess('School program attachments successfully created');
    }



    public function destroy(Request $request, DeleteSchoolProgramAttachAction $deleteSchoolProgramAttachAction, LogService $log_service)
    {
        $school_id = $request->route('school');
        $sch_prog_id = $request->route('sch_prog');
        $attach_id = $request->route('attach');

        DB::beginTransaction();
        try {

            $deleteSchoolProgramAttachAction->execute($attach_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_SCHOOL_PROGRAM_ATTACH, $e->getMessage(), $e->getLine(), $e->getFile(), ['attach_id' => $attach_id]);
            return Redirect::to('program/school/' . $school_id . '/detail/' . $sch_prog_id)->withError('Failed to delete school program attachments' . $e->getMessage());
        }

        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_SCHOOL_PROGRAM_ATTACH, 'School program attach has been deleted', ['attach_id' => $attach_id]);

        return Redirect::to('program/school/' . $school_id . '/detail/' . $sch_prog_id)->withSuccess('School program attachments successfully deleted');
    }
}

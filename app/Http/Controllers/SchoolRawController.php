<?php

namespace App\Http\Controllers;

use App\Actions\Schools\Raw\DeleteSchoolRawAction;
use App\Actions\Schools\Raw\UpdateSchoolRawAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreSchoolRawRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Jobs\RawClient\ProcessVerifyClient;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolRawController extends Controller
{
    use LoggingTrait;
    protected SchoolRepositoryInterface $schoolRepository;
    protected ClientRepositoryInterface $clientRepository;
    protected DeleteSchoolRawAction $deleteSchoolRawAction;

    public function __construct(SchoolRepositoryInterface $schoolRepository, ClientRepositoryInterface $clientRepository, DeleteSchoolRawAction $deleteSchoolRawAction)
    {
        $this->schoolRepository = $schoolRepository;
        $this->clientRepository = $clientRepository;
        $this->deleteSchoolRawAction = $deleteSchoolRawAction;
    }

    public function index(Request $request)
    {
        if ($request->ajax())
            return $this->schoolRepository->getAllSchoolDataTables(true);

        $duplicates_schools = $this->schoolRepository->getDuplicateUnverifiedSchools();
        $duplicates_schools_string = $this->fnConvertDuplicatesSchoolAsString($duplicates_schools);

        return view('pages.instance.school.raw.index')->with(
            [
                'duplicates_schools_string' => $duplicates_schools_string,
                'duplicates_schools' => $duplicates_schools->pluck('sch_name')->toArray()
            ]
        );
    }

    private function fnConvertDuplicatesSchoolAsString($schools)
    {
        $response = '';
        foreach ($schools as $school) {

            $response .= ', '.$school->sch_name;

        }

        return $response;
    }

    public function create(Request $request)
    {
        return view('pages.instance.school.raw.form-new');
    }

    public function update(StoreSchoolRawRequest $request, UpdateSchoolRawAction $updateSchoolRawAction, LogService $log_service)
    {
        $school_details = $request->only([
            'sch_name',
            'sch_type',
            'sch_location',
            'sch_score',
        ]);

        $school_id = $request->route('raw');

        DB::beginTransaction();
        try {

            $updated_school_raw = $updateSchoolRawAction->execute($school_id, $school_details);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_SCHOOL_RAW, $e->getMessage(), $e->getLine(), $e->getFile(), $school_details);

            return Redirect::to('instance/school/raw')->withError('Failed to convert school');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_SCHOOL_RAW, 'School raw has been updated', $updated_school_raw->toArray());

        return Redirect::to('instance/school/raw')->   withSuccess('Convert raw school success');
    }


    public function destroy (Request $request, LogService $log_service)
    {
        # when is method 'POST' meaning the function come from bulk delete
        $isBulk = $request->isMethod('POST') ? true : false;
        if ($isBulk)
            return $this->bulk_destroy($request, $log_service); 
        
        return $this->single_destroy($request, $log_service);

    }

    private function single_destroy(Request $request, LogService $log_service)
    {
        $raw_school_id = $request->route('raw');
        if (!$school = $this->schoolRepository->findUnverifiedSchool($raw_school_id))
            Redirect::back()->withError('School does not exists');

        DB::beginTransaction();
        try {

            $this->deleteSchoolRawAction->execute(false, $raw_school_id, null);

            DB::commit();

        } catch (Exception $e) {
         
            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_SINGLE_SCHOOL_RAW, $e->getMessage(), $e->getLine(), $e->getFile(), ['raw_school_id' => $raw_school_id]);

            return Redirect::to('instance/school/raw')->withError('Failed to delete raw school');

        }

        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_SINGLE_SCHOOL_RAW, 'School raw has been single deleted', ['raw_school_id' => $raw_school_id]);
        
        return Redirect::to('instance/school/raw')->   withSuccess('Delete raw school success');
    }

    private function bulk_destroy(Request $request, LogService $log_service)
    {
        # raw school id that being choose from list raw data school
        $raw_school_ids = $request->choosen;
        DB::beginTransaction();
        try {

            $this->deleteSchoolRawAction->execute(true, null, $raw_school_ids);

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_BULK_SCHOOL_RAW, $e->getMessage(), $e->getLine(), $e->getFile(), ['school_ids' => $raw_school_ids]);

            return response()->json(['success' => false, 'message' => 'Failed to delete raw school'], 500);

        }

        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_BULK_SCHOOL_RAW, 'School raw has been bulk deleted', ['school_ids' => $raw_school_ids]);

        return response()->json(['success' => true, 'message' => 'Delete raw school success']);
    }

}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolRawRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Jobs\RawClient\ProcessVerifyClient;
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

    public function __construct(SchoolRepositoryInterface $schoolRepository, ClientRepositoryInterface $clientRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->clientRepository = $clientRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax())
            return $this->schoolRepository->getAllSchoolDataTables(true);

        $duplicates_schools = $this->schoolRepository->getDuplicateUnverifiedSchools();
        $duplicates_schools_string = $this->convertDuplicatesSchoolAsString($duplicates_schools);

        return view('pages.instance.school.raw.index')->with(
            [
                'duplicates_schools_string' => $duplicates_schools_string,
                'duplicates_schools' => $duplicates_schools->pluck('sch_name')->toArray()
            ]
        );
    }

    private function convertDuplicatesSchoolAsString($schools)
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

    public function update(StoreSchoolRawRequest $request)
    {
        $schoolDetails = $request->only([
            'sch_name',
            'sch_type',
            'sch_location',
            'sch_score',
        ]);

        $schoolId = $request->route('raw');
        $oldSchool = $this->schoolRepository->getSchoolById($schoolId);

        DB::beginTransaction();
        try {

            # insert into school
            $this->schoolRepository->updateSchool($schoolId, $schoolDetails + ['is_verified' => 'Y']);


            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Convert raw school failed : ' . $e->getMessage());
            return Redirect::to('instance/school/raw')->withError('Failed to convert school');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'School', Auth::user()->first_name . ' ' . Auth::user()->last_name, $schoolDetails, $oldSchool);

        return Redirect::to('instance/school/raw')->   withSuccess('Convert raw school success');
    }


    public function destroy (Request $request)
    {
        # when is method 'POST' meaning the function come from bulk delete
        $isBulk = $request->isMethod('POST') ? true : false;
        if ($isBulk)
            return $this->bulk_destroy($request); 
        
        return $this->single_destroy($request);

    }

    private function single_destroy(Request $request)
    {
        $rawSchoolId = $request->route('raw');
        if (!$school = $this->schoolRepository->findUnverifiedSchool($rawSchoolId))
            Redirect::back()->withError('School does not exists');

        DB::beginTransaction();
        try {

            $this->schoolRepository->moveToTrash($rawSchoolId);
            
            # get all client that tagged with the school
            # and remove the school that being deleted
            $clients = $this->clientRepository->getClientBySchool($rawSchoolId)->pluck('id')->toArray();
            $this->clientRepository->updateClients($clients, ['sch_id' => NULL]);

            ProcessVerifyClient::dispatch($clients)->onQueue('verifying-client');
            DB::commit();

        } catch (Exception $e) {
         
            DB::rollBack();
            Log::error('Failed to delete raw school failed : ' . $e->getMessage());
            return Redirect::to('instance/school/raw')->withError('Failed to delete raw school');

        }

        return Redirect::to('instance/school/raw')->   withSuccess('Delete raw school success');
    }

    private function bulk_destroy(Request $request)
    {
        # raw school id that being choose from list raw data school
        $rawSchoolIds = $request->choosen;
        DB::beginTransaction();
        try {

            $this->schoolRepository->moveBulkToTrash($rawSchoolIds);

            # get all client that tagged with the school
            # and remove the school that being deleted
            $clients = $this->clientRepository->getClientInSchool($rawSchoolIds)->pluck('id')->toArray();
            $this->clientRepository->updateClients($clients, ['sch_id' => NULL]);

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to bulk delete raw school failed : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete raw school']);

        }

        return response()->json(['success' => true, 'message' => 'Delete raw school success']);
    }

}

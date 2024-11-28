<?php

namespace App\Http\Controllers;

use App\Actions\Schools\Alias\CreateSchoolAliasAction;
use App\Actions\Schools\Alias\DeleteSchoolAliasAction;
use App\Enum\LogModule;
use App\Http\Requests\SchoolAliasRequest;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Jobs\RawClient\ProcessVerifyClient;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolAliasController extends Controller
{

    protected SchoolRepositoryInterface $schoolRepository;
    protected ClientRepositoryInterface $clientRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, ClientRepositoryInterface $clientRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->clientRepository = $clientRepository;
    }

    public function store(SchoolAliasRequest $request, CreateSchoolAliasAction $createSchoolAliasAction, LogService $log_service)
    {
        $school_id = $request->route('school');
        if (!$this->schoolRepository->getSchoolById($school_id))
            return Redirect::back()->withError('There is no such school');

        DB::beginTransaction();
        try {

            $alias = $request->alias;

            $details = [
                'sch_id' => $school_id,
                'alias' => $alias
            ];

            if ($request->is_convert) {
                $main_school = $request->school;
                $details['sch_id'] = $main_school;
            }

            $created_new_alias = $createSchoolAliasAction->execute($request, $details);
            
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_SCHOOL_ALIAS, $e->getMessage(), $e->getLine(), $e->getFile(), $details);

            return Redirect::back()->withError('Failed to store new alias');

        }

        $school = $this->schoolRepository->getSchoolById($details['sch_id']);

        # create log success
        $log_service->createSuccessLog(LogModule::STORE_SCHOOL_ALIAS, 'New school alias has been added', $created_new_alias->toArray());

        return redirect()->route('school.index')->withSuccess('New alias has been added to '.$school->sch_name);

    }

    public function destroy(Request $request, DeleteSchoolAliasAction $deleteSchoolAliasAction, LogService $log_service)
    {
        $school_id = $request->route('school');
        if (!$this->schoolRepository->getSchoolById($school_id))
            return Redirect::back()->withError('There is no such school');
        
        $alias_id = $request->route('alias');

        DB::beginTransaction();
        try {

            $deleteSchoolAliasAction->execute($alias_id);

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();

            $log_service->createErrorLog(LogModule::DELETE_SCHOOL_ALIAS, $e->getMessage(), $e->getLine(), $e->getFile(), ['school_id' => $school_id, 'alias_id' => $alias_id]);

            return response()->json(['message' => 'Failed to delete alias'], 500);

        }

        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_SCHOOL_ALIAS, 'School alias has been deleted', ['school_id' => $school_id, 'alias_id' => $alias_id]);

        return response()->json(['message' => 'The alias has been removed']);

    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolAliasRequest;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
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

    public function store(SchoolAliasRequest $request)
    {
        $schoolId = $request->route('school');
        if (!$this->schoolRepository->getSchoolById($schoolId))
            return Redirect::back()->withError('There is no such school');

        $alias = $request->alias;

        DB::beginTransaction();
        try {

            $details = [
                'sch_id' => $schoolId,
                'alias' => $alias
            ];

            if ($request->is_convert) {
                $main_school = $request->school;
                $details['sch_id'] = $main_school;
            }

            $this->schoolRepository->createNewAlias($details);

            # if is_convert is true
            # meaning that they store from form-alias on list school
            # meaning the raw school must be deleted
            if ($request->is_convert) {
                $rawSchId = $request->raw_sch_id;

                # getting all client that has deleted (soon) school
                $clientIds = $this->clientRepository->getClientBySchool($rawSchId)->pluck('id')->toArray();
                $this->clientRepository->updateClients($clientIds, ['sch_id' => $details['sch_id']]);

                # delete raw school
                $this->schoolRepository->deleteSchool($rawSchId);
            }
            # end process from convert to alias
            
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to store new alias : '.$e->getMessage().' on line '.$e->getLine());
            return Redirect::back()->withError('Failed to store new alias');

        }

        return redirect()->route('school.show', ['school' => $schoolId])->withSuccess('New alias has been added');

    }

    public function destroy(Request $request)
    {
        $schoolId = $request->route('school');
        if (!$this->schoolRepository->getSchoolById($schoolId))
            return Redirect::back()->withError('There is no such school');
        
        $aliasId = $request->route('alias');

        DB::beginTransaction();
        try {

            $this->schoolRepository->deleteAlias($aliasId);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to delete alias : '.$e->getMessage().' on line '.$e->getLine());
            return response()->json(['message' => 'Failed to delete alias'], 500);

        }

        return response()->json(['message' => 'The alias has been removed']);

    }
}

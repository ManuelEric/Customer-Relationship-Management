<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolAliasRequest;
use App\Interfaces\SchoolRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolAliasController extends Controller
{

    protected SchoolRepositoryInterface $schoolRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
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

            $this->schoolRepository->createNewAlias($details);
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

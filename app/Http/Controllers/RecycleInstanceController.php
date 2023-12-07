<?php

namespace App\Http\Controllers;

use App\Interfaces\SchoolRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class RecycleInstanceController extends Controller
{

    protected SchoolRepositoryInterface $schoolRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }
    
    public function index(Request $request)
    {
        $target = $request->route('target');
        switch ($target) {

            case "school":
                $model = $this->schoolRepository->getDeletedSchools(true);
                $view = 'pages.recycle.instance.school';
                break;

        }

        if ($request->ajax())
            return $this->schoolRepository->getDataTables($model);

        return view($view);
    }

    public function restore(Request $request)
    {
        $target = $request->route('target'); # not used
        $instanceId = $request->route('instance');

        switch ($target) {

            case "school":
                $repository = 'schoolRepository';
                $find_function = 'findDeletedSchoolById';
                $restore_function = 'restoreSchool';
                break;

            # if there are another instances
            # put another target here

        }

        if (!$this->{$repository}->{$find_function}($instanceId))
            abort(404);

        DB::beginTransaction();
        try {

            $this->{$repository}->{$restore_function}($instanceId);
            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::error('Failed to restore '.$target.' : ' . $e->getMessage().' on line '.$e->getLine());
            return Redirect::back()->withError('Failed to restore '.$target);

        }

        return Redirect::to('recycle/instance/'.$target)->withSuccess(ucfirst($target) . ' has been restored');
    }
}

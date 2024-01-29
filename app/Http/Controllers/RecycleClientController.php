<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class RecycleClientController extends Controller
{
    protected ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }
    
    public function index(Request $request)
    {
        $target = $request->route('target');
        $entries = [];
        switch ($target) {

            case "students":
                
                if ($request->ajax()){
                    # advanced filter purpose
                    $school_name = $request->get('school_name');
                    $graduation_year = $request->get('graduation_year');
                    $leads = $request->get('lead_source');
                    $initial_programs = $request->get('program_suggest');
                    $status_lead = $request->get('status_lead');
                    $active_status = $request->get('active_status');
                    $pic = $request->get('pic');
                    $start_joined_date = $request->get('start_joined_date');
                    $end_joined_date = $request->get('end_joined_date');
                    $start_deleted_date = $request->get('start_deleted_date');
                    $end_deleted_date = $request->get('end_deleted_date');

                    # array for advanced filter request
                    $advanced_filter = [
                        'school_name' => $school_name,
                        'graduation_year' => $graduation_year,
                        'leads' => $leads,
                        'initial_programs' => $initial_programs,
                        'status_lead' => $status_lead,
                        'active_status' => $active_status,
                        'pic' => $pic,
                        'start_joined_date' => $start_joined_date,
                        'end_joined_date' => $end_joined_date,
                        'start_deleted_date' => $start_deleted_date,
                        'end_deleted_date' => $end_deleted_date
                    ];

                    $model = $this->clientRepository->getDeletedStudents(true, $advanced_filter);
                }

                $view = 'pages.recycle.client.student';
                $entries = app('App\Services\ClientStudentService')->getClientStudent();
                break;


            case "parents":
                $model = $this->clientRepository->getDeletedParents(true);
                $view = 'pages.recycle.client.parent';
                break;

            
            case "teacher-counselor":
                $model = $this->clientRepository->getDeletedTeachers(true);
                $view = 'pages.recycle.client.teacher';
                break;

        }

        if ($request->ajax()) 
            return $this->clientRepository->getDataTables($model);

        return view($view)->with($entries);
    }


    public function restore(Request $request)
    {
        $target = $request->route('target'); # not used
        $clientId = $request->route('client');

        if (!$this->clientRepository->findDeletedClientById($clientId))
            abort(404);

        DB::beginTransaction();
        try {

            $this->clientRepository->restoreClient($clientId);
            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::error('Failed to restore '.$target.' : ' . $e->getMessage().' on line '.$e->getLine());
            return Redirect::back()->withError('Failed to restore client');

        }

        return Redirect::to('recycle/client/'.$target)->withSuccess('Client has been restored');
    }   
}

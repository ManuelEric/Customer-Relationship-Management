<?php

namespace App\Http\Controllers;

use App\Enum\LogModule;
use App\Interfaces\ClientRepositoryInterface;
use App\Jobs\Client\ProcessInsertLogClient;
use App\Services\Log\LogService;
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
                $entries = app('App\Services\ClientStudentService')->advancedFilterClient();
                break;


            case "parents":
                $model = $this->clientRepository->getDeletedParents(true);
                $view = 'pages.recycle.client.parent';
                break;

            
            case "teacher-counselor":
                $model = $this->clientRepository->getDeletedTeachers(true);
                $view = 'pages.recycle.client.teacher';
                break;

            default:
                return Redirect::to('recycle/client/students');

        }

        if ($request->ajax()) 
            return $this->clientRepository->getDataTables($model);

        return view($view)->with($entries);
    }


    public function restore(
        Request $request,
        LogService $log_service
        )
    {
        $target = $request->route('target'); # not used
        $client_id = $request->route('client');
        $redirect_page = $this->page($target);

        if (!$this->clientRepository->findDeletedClientById($client_id))
            abort(404);

        DB::beginTransaction();
        try {

            $the_user = $this->clientRepository->restoreClient($client_id);
            
            $client_data_for_log[] = [
                'client_id' => $the_user->id,
                'first_name' => $the_user->first_name,
                'last_name' => $the_user->last_name,
                'lead_source' => $the_user->lead_id,
                'inputted_from' => 'restore',
            ];

            # Trigger to insert log client
            ProcessInsertLogClient::dispatch($client_data_for_log)->onQueue('insert-log-client');

            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            $this->storeErrorLog($log_service, $target, $e, ['client_id' => $client_id]);
            return Redirect::back()->withError("Failed to restore {$target}");
        }
        
        $this->storeSuccessLog($log_service, $target, $the_user->toArray());
        return Redirect::to('recycle/client/'.$redirect_page)->withSuccess("{$target} has been restored");
    }

    private function page($client_type)
    {
        switch ( $client_type )
        {
            case "student":
                $page = "students";
                break;
            case "parent":
                $page = "parents";
                break;
            case "teacher":
                $page = "teacher-counselor";
                break;
        }
        return $page;
    }

    private function storeSuccessLog($service, $client_type, $data = [])
    {
        switch ($client_type) {
            case "student":
                $service->createSuccessLog(LogModule::RESTORE_STUDENT, "The {$client_type} has been restored", $data);
                break;
            case "parent":
                $service->createSuccessLog(LogModule::RESTORE_PARENT, "The {$client_type} has been restored", $data);
                break;
            case "teacher":
                $service->createSuccessLog(LogModule::RESTORE_TEACHER, "The {$client_type} has been restored", $data);
                break;
        }
    }

    private function storeErrorLog($service, $client_type, $error, $data = [])
    {
        switch ($client_type) {
            case "student":
                $service->createErrorLog(LogModule::RESTORE_STUDENT, $error->getMessage(), $error->getLine(), $error->getFile(), $data);
                break;  
            case "parent":
                $service->createErrorLog(LogModule::RESTORE_PARENT, $error->getMessage(), $error->getLine(), $error->getFile(), $data);
                break;  
            case "teacher":
                $service->createErrorLog(LogModule::RESTORE_TEACHER, $error->getMessage(), $error->getLine(), $error->getFile(), $data);
                break;  
        }
    }
}

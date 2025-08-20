<?php

namespace App\Http\Controllers;

use App\Enum\LogModule;
use App\Interfaces\ClientRepositoryInterface;
use App\Jobs\Client\ProcessInsertLogClient;
use App\Services\ClientStudentService;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class RecycleClientController extends Controller
{
    protected ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function index(
        Request $request,
        ClientStudentService $clientStudentService
    ) {
        $target = $request->route('target');

        if ($target === 'students' && $request->ajax()) {
            // Collect advanced filter inputs
            $advanced_filter = $request->only([
                'school_name',
                'graduation_year',
                'lead_source',
                'program_suggest',
                'status_lead',
                'active_status',
                'pic',
                'start_joined_date',
                'end_joined_date',
                'start_deleted_date',
                'end_deleted_date',
            ]);

            // Normalize keys (optional: rename to match expected keys)
            $advanced_filter = [
                'school_name' => $advanced_filter['school_name'] ?? null,
                'graduation_year' => $advanced_filter['graduation_year'] ?? null,
                'leads' => $advanced_filter['lead_source'] ?? null,
                'initial_programs' => $advanced_filter['program_suggest'] ?? null,
                'status_lead' => $advanced_filter['status_lead'] ?? null,
                'active_status' => $advanced_filter['active_status'] ?? null,
                'pic' => $advanced_filter['pic'] ?? null,
                'start_joined_date' => $advanced_filter['start_joined_date'] ?? null,
                'end_joined_date' => $advanced_filter['end_joined_date'] ?? null,
                'start_deleted_date' => $advanced_filter['start_deleted_date'] ?? null,
                'end_deleted_date' => $advanced_filter['end_deleted_date'] ?? null,
            ];

            return $this->clientRepository->getDeletedStudents(true, $advanced_filter);
        }

        $views = [
            'students' => 'pages.recycle.client.student',
            'parents' => 'pages.recycle.client.parent',
            'teacher-counselor' => 'pages.recycle.client.teacher',
        ];

        $models = [
            'parents' => $this->clientRepository->getDeletedParents(true),
            'teacher-counselor' => $this->clientRepository->getDeletedTeachers(true),
        ];

        // Redirect to default if unknown target
        if (! array_key_exists($target, $views)) {
            return Redirect::to('recycle/client/students');
        }

        $view = $views[$target];

        if ($target === 'students') {
            $entries = Cache::remember('global:advanced_filter', 60 * 15, function () use ($clientStudentService) {
                return $clientStudentService->advancedFilterClient();
            });
        } else {
            $model = $models[$target];
        }

        if ($request->ajax()) {
            return $this->clientRepository->getTrashDataTables($model ?? null, true);
        }

        return view($view)->with($entries ?? []);
    }

    public function restore(
        Request $request,
        LogService $log_service
    ) {
        $target = $request->route('target'); // not used
        $client_id = $request->route('client');
        $redirect_page = $this->page($target);

        if (! $this->clientRepository->findDeletedClientById($client_id)) {
            abort(404);
        }

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

            // Trigger to insert log client
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
        switch ($client_type) {
            case 'student':
                $page = 'students';
                break;
            case 'parent':
                $page = 'parents';
                break;
            case 'teacher':
                $page = 'teacher-counselor';
                break;
            default:
                $page = 'students';
        }

        return $page;
    }

    private function storeSuccessLog($service, $client_type, $data = [])
    {
        switch ($client_type) {
            case 'student':
                $service->createSuccessLog(LogModule::RESTORE_STUDENT, "The {$client_type} has been restored", $data);
                break;
            case 'parent':
                $service->createSuccessLog(LogModule::RESTORE_PARENT, "The {$client_type} has been restored", $data);
                break;
            case 'teacher':
                $service->createSuccessLog(LogModule::RESTORE_TEACHER, "The {$client_type} has been restored", $data);
                break;
        }
    }

    private function storeErrorLog($service, $client_type, $error, $data = [])
    {
        switch ($client_type) {
            case 'student':
                $service->createErrorLog(LogModule::RESTORE_STUDENT, $error->getMessage(), $error->getLine(), $error->getFile(), $data);
                break;
            case 'parent':
                $service->createErrorLog(LogModule::RESTORE_PARENT, $error->getMessage(), $error->getLine(), $error->getFile(), $data);
                break;
            case 'teacher':
                $service->createErrorLog(LogModule::RESTORE_TEACHER, $error->getMessage(), $error->getLine(), $error->getFile(), $data);
                break;
        }
    }
}

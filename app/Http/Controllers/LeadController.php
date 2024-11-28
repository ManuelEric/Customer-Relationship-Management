<?php

namespace App\Http\Controllers;

use App\Actions\Leads\CreateLeadAction;
use App\Actions\Leads\DeleteLeadAction;
use App\Actions\Leads\UpdateLeadAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\CreateReferralCodeTrait;
use App\Http\Traits\GetDepartmentFromLoggedInUser;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Models\Lead;
use App\Services\Log\LogService;
use App\Services\Master\LeadService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class LeadController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use LoggingTrait;
    use CreateReferralCodeTrait;

    private LeadRepositoryInterface $leadRepository;
    private DepartmentRepositoryInterface $departmentRepository;
    private ClientRepositoryInterface $clientRepository;
    private LeadService $leadService;

    public function __construct(LeadRepositoryInterface $leadRepository, DepartmentRepositoryInterface $departmentRepository, ClientRepositoryInterface $clientRepository, LeadService $leadService)
    {
        $this->leadRepository = $leadRepository;
        $this->departmentRepository = $departmentRepository;
        $this->clientRepository = $clientRepository;
        $this->leadService = $leadService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->leadRepository->getAllLeadDataTables();
        }

        $departments = $this->departmentRepository->getAllDepartment();

        return view('pages.master.lead.index')->with(
            [
                'departments' => $departments
            ]
        );
    }

    public function store(StoreLeadRequest $request, CreateLeadAction $createLeadAction, LogService $log_service)
    {
        $new_lead_details = $request->safe()->only([
            'lead_name',
            'score',
            'kol',
            'department_id',
        ]);

        DB::beginTransaction();
        try {

            $new_lead = $createLeadAction->execute($request, $new_lead_details);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_LEAD, $e->getMessage(), $e->getLine(), $e->getFile(), $new_lead_details);

            Log::error('Store lead failed : ' . $e->getMessage());
            return Redirect::to('master/lead')->withError('Failed to create a new lead');
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_LEAD, 'New asset has been added', $new_lead->toArray());

        return Redirect::to('master/lead')->withSuccess('Lead successfully created');
    }

    public function create()
    {
        return view('pages.master.lead.form');
    }

    public function show(Request $request)
    {
        $lead_id = $request->route('lead');

        # retrieve lead data by id
        $lead = $this->leadRepository->getLeadById($lead_id);

        return response()->json(['lead' => $lead]);
    }

    public function edit(Request $request)
    {
        if ($request->ajax()) {
            return $this->leadRepository->getAllLeadDataTables();
        }

        $lead_id = $request->route('lead');

        # retrieve lead data by id
        $lead = $this->leadRepository->getLeadById($lead_id);
        # put the link to update lead form below
        # example

        return view('pages.master.lead.index')->with(
            [
                'lead' => $lead
            ]
        );
    }

    public function update(StoreLeadRequest $request, UpdateLeadAction $updateLeadAction, LogService $log_service)
    {
        $new_lead_details = $request->only([
            'lead_name',
            'score',
            'kol',
            'department_id',
        ]);

        # retrieve lead id from url
        $lead_id = $request->route('lead');
        
        DB::beginTransaction();
        try {

            $updated_lead = $updateLeadAction->execute($request, $lead_id, $new_lead_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_LEAD, $e->getMessage(), $e->getLine(), $e->getFile(), $new_lead_details);

            return Redirect::to('master/lead')->withError('Failed to update lead');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_LEAD, 'Lead has been updated', $updated_lead->toArray());

        return Redirect::to('master/lead')->withSuccess('Lead successfully updated');
    }

    public function destroy(Request $request, DeleteLeadAction $deleteLeadAction, LogService $log_service)
    {
        $lead_id = $request->route('lead');
        $lead = $this->leadRepository->getLeadById($lead_id);

        DB::beginTransaction();
        try {

            $deleteLeadAction->execute($lead_id);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_LEAD, $e->getMessage(), $e->getLine(), $e->getFile(), $lead->toArray());

            return Redirect::to('master/lead')->withError('Failed to delete lead');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_LEAD, 'Lead has been deleted', $lead->toArray());

        return Redirect::to('master/lead')->withSuccess('Lead successfully deleted');
    }

    public function fnGetListReferral(Request $request)
    {
        $this->leadService->snGetListReferral($request);
    }
}
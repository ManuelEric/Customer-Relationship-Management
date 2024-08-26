<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\CreateReferralCodeTrait;
use App\Http\Traits\GetDepartmentFromLoggedInUser;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\LeadRepositoryInterface;
use App\Models\Lead;
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

    public function __construct(LeadRepositoryInterface $leadRepository, DepartmentRepositoryInterface $departmentRepository, ClientRepositoryInterface $clientRepository)
    {
        $this->leadRepository = $leadRepository;
        $this->departmentRepository = $departmentRepository;
        $this->clientRepository = $clientRepository;
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

    public function store(StoreLeadRequest $request)
    {
        $leadDetails = $request->only([
            'lead_name',
            'score',
            'kol',
            'department_id',
        ]);

        $last_id = Lead::max('lead_id');
        if (!$last_id)
            $last_id = 'LS000';

        if ($request->kol == true) {

            $leadDetails['main_lead'] = "KOL";
            $leadDetails['sub_lead'] = $request->lead_name;
        } else {
            $leadDetails['main_lead'] = $request->lead_name;
            $leadDetails['sub_lead'] = null;
        }

        $lead_id_without_label = $this->remove_primarykey_label($last_id, 2);
        $lead_id_with_label = 'LS' . $this->add_digit($lead_id_without_label + 1, 3);

        DB::beginTransaction();
        try {

            $newDataLead = $this->leadRepository->createLead(['lead_id' => $lead_id_with_label] + $leadDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store lead failed : ' . $e->getMessage());
            return Redirect::to('master/lead')->withError('Failed to create a new lead');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Lead', Auth::user()->first_name . ' '. Auth::user()->last_name, $newDataLead);

        return Redirect::to('master/lead')->withSuccess('Lead successfully created');
    }

    public function create()
    {
        return view('pages.master.lead.form');
    }

    public function show(Request $request)
    {
        $leadId = $request->route('lead');

        # retrieve lead data by id
        $lead = $this->leadRepository->getLeadById($leadId);

        return response()->json(['lead' => $lead]);
    }

    public function edit(Request $request)
    {
        if ($request->ajax()) {
            return $this->leadRepository->getAllLeadDataTables();
        }

        $leadId = $request->route('lead');

        # retrieve lead data by id
        $lead = $this->leadRepository->getLeadById($leadId);
        # put the link to update lead form below
        # example

        return view('pages.master.lead.index')->with(
            [
                'lead' => $lead
            ]
        );
    }

    public function update(StoreLeadRequest $request)
    {
        $leadDetails = $request->only([
            'lead_name',
            'score',
            'kol',
            'department_id',
        ]);

        # retrieve lead id from url
        $leadId = $request->route('lead');
        
        $oldLead = $this->leadRepository->getLeadById($leadId);

        if ($request->kol == true) {

            $leadDetails['main_lead'] = "KOL";
            $leadDetails['sub_lead'] = $request->lead_name;
        } else {
            $leadDetails['main_lead'] = $request->lead_name;
            $leadDetails['sub_lead'] = null;
        }

        DB::beginTransaction();
        try {

            $this->leadRepository->updateLead($leadId, $leadDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update lead failed : ' . $e->getMessage());
            return Redirect::to('master/lead')->withError('Failed to update lead');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Lead', Auth::user()->first_name . ' '. Auth::user()->last_name, $leadDetails, $oldLead);

        return Redirect::to('master/lead')->withSuccess('Lead successfully updated');
    }

    public function destroy(Request $request)
    {
        $leadId = $request->route('lead');
        $lead = $this->leadRepository->getLeadById($leadId);

        DB::beginTransaction();
        try {

            $this->leadRepository->deleteLead($leadId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete lead failed : ' . $e->getMessage());
            return Redirect::to('master/lead')->withError('Failed to delete lead');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Curriculum', Auth::user()->first_name . ' '. Auth::user()->last_name, $lead);

        return Redirect::to('master/lead')->withSuccess('Lead successfully deleted');
    }

    public function getListReferral(Request $request)
    {
        $grouped =  new Collection();

        if($request->ajax())
        {
            $filter['full_name'] = trim($request->term);
            $listReferral = $this->clientRepository->getListReferral(['id', 'first_name', 'last_name'], $filter);
    
            $grouped = $listReferral->mapToGroups(function ($item, $key) {
                return [
                    $item['data'] . 'results' => [
                        'id' => isset($item['id']) ? $this->createReferralCode($item['first_name'], $item['id']) : null,
                        'text' => isset($item['first_name']) ? $item['first_name'] . ' ' . $item['last_name'] : null
                    ],
                ];
            });
    
            $morePages=true;
               if (empty($listReferral->nextPageUrl())){
                $morePages=false;
               }
    
            $grouped['pagination'] = [
                'more' => $morePages
            ];
    
            return $grouped;
         
        }
    }
}
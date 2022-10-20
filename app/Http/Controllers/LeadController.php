<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\LeadRepositoryInterface;
use App\Models\Lead;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class LeadController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    
    private LeadRepositoryInterface $leadRepository;

    public function __construct(LeadRepositoryInterface $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->leadRepository->getAllLeadDataTables();
        }

        return view('pages.lead.index');
    }

    public function store(StoreLeadRequest $request)
    {
        $leadDetails = $request->only([
            'main_lead',
            'sub_lead',
            'score',
            'kol',
        ]);

        $last_id = Lead::max('lead_id');
        if (!$last_id)
            $last_id = 'LS000';

        if ($request->kol == true)
            $leadDetails['main_lead'] = "KOL";
        else
            $leadDetails['sub_lead'] = null;
        
        $lead_id_without_label = $this->remove_primarykey_label($last_id, 2);
        $lead_id_with_label = 'LS' . $this->add_digit($lead_id_without_label + 1, 3);

        DB::beginTransaction();
        try {

            $this->leadRepository->createLead(['lead_id' => $lead_id_with_label] + $leadDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store lead failed : ' . $e->getMessage());
        }

        return Redirect::to('master/lead')->withSuccess('Lead successfully created');
    }

    public function create()
    {
        return view('pages.lead.form');
    }

    public function edit(Request $request)
    {
        $leadId = $request->route('lead');

        # retrieve lead data by id
        $lead = $this->leadRepository->getLeadById($leadId);
        # put the link to update lead form below
        # example

        return view('lead.form')->with(
            [
                'lead' => $lead
            ]
        );
    }

    public function update(StoreLeadRequest $request)
    {
        $leadDetails = $request->only([
            'main_lead',
            'sub_lead',
            'score',
            'kol',
        ]);

        # retrieve lead id from url
        $leadId = $request->route('lead');

        if ($request->kol == true)
            $leadDetails['main_lead'] = "KOL";
        else
            $leadDetails['sub_lead'] = null;

        DB::beginTransaction();
        try {

            $this->leadRepository->updateLead($leadId, $leadDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update lead failed : ' . $e->getMessage());
        }

        return Redirect::to('master/lead')->withSuccess('Lead successfully updated');
    }

    public function destroy(Request $request)
    {
        $leadId = $request->route('lead');

        DB::beginTransaction();
        try {

            $this->leadRepository->deleteLead($leadId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete lead failed : ' . $e->getMessage());
        }

        return Redirect::to('master/lead')->withSuccess('Lead successfully deleted');
    }
}

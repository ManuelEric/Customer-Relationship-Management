<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFollowupRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\FollowupRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class FollowupClientController extends Controller
{
    use LoggingTrait;
    private FollowupRepositoryInterface $followupRepository;

    public function __construct(FollowupRepositoryInterface $followupRepository)
    {
        $this->followupRepository = $followupRepository;
    }
    
    public function store(Request $request)
    {
        if (!$clientId = $request->route('student'))
            abort(404);

        $rules = [
            'followup_date' => 'nullable',
            'notes' => 'nullable',
            'status' => 'required|in:0,1,2,3|integer'
        ];

        $incomingRequest = $request->only(['followup_date', 'notes', 'status']);

        $validator = Validator::make($incomingRequest, $rules);
        
        # threw error if validation fails
        if ($validator->fails()) {
            return Redirect::back()->withError('Invalid request.');
        }

        # after validating incoming request data, then retrieve the incoming request data
        $validated = $request->collect();

        DB::beginTransaction();
        try {

            $details = [
                'user_id' => Auth::user()->id,
                'client_id' => $clientId,
                'followup_date' => $validated['followup_date'],
                'notes' => $validated['notes'],
                'status' => $validated['status']
            ];
    
    
            $created_followup = $this->followupRepository->create($details);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to process followup appointment. Issue : '.$e->getMessage() . ' from '. $e->getFile(). ' on line '.$e->getLine());
            return Redirect::back()->withError('Cannot process the request.');

        }

        $this->logSuccess('store', 'Board', 'Followup Client', Auth::user()->email, $created_followup);
        return Redirect::to('client/board')->withSuccess('Appointment successfully processed.');
    }

    public function update(Request $request)
    {
        if (!$clientId = $request->route('student'))
            abort(404);

        if (!$followupId = $request->route('followup'))
            abort(404);

        $rules = [
            'minutes_of_meeting' => 'nullable',
            'status' => 'required|in:0,1,2,3|integer'
        ];

        $incomingRequest = $request->only(['minutes_of_meeting', 'status']);

        $validator = Validator::make($incomingRequest, $rules);
        
        # threw error if validation fails
        if ($validator->fails()) {
            return Redirect::back()->withError('Cannot process the request.');
        }

        # after validating incoming request data, then retrieve the incoming request data
        $validated = $request->collect();

        DB::beginTransaction();
        try {

            $before_update = $this->followupRepository->findFollowupClient($followupId);

            $newDetails = [
                'minutes_of_meeting' => $validated['minutes_of_meeting'],
                'status' => $validated['status']
            ];
    
            $updated_followup = $this->followupRepository->update($followupId, $newDetails);
            
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to update followup appointment. Issue : '.$e->getMessage() . ' from '. $e->getFile(). ' on line '.$e->getLine());
            return Redirect::back()->withError('Cannot process the request.');

        }

        $this->logSuccess('update', 'Board', 'Followup Client', Auth::user()->email, $updated_followup, $before_update);
        return Redirect::to('client/board')->withSuccess('Appointment successfully updated.');
    }
}

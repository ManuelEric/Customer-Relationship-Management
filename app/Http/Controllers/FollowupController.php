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

class FollowupController extends Controller
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

    # follow up plan for client program
    // public function store(StoreFollowupRequest $request)
    // {
    //     $studentId = $request->route('student');
    //     $clientProgramId = $request->route('program');

    //     $followupDetails = $request->only([
    //         'followup_date',
    //         'notes',
    //     ]);

    //     DB::beginTransaction();
    //     try {

    //         $this->followupRepository->createFollowup(['clientprog_id' => $clientProgramId] + $followupDetails);
    //         DB::commit();           

    //     } catch (Exception $e) {

    //         DB::rollBack();
    //         Log::error('Store followup plan failed : ' . $e->getMessage());
    //         return Redirect::to('client/student/' . $studentId . '/program/' . $clientProgramId)->withError('Failed to add followup plan');

    //     }

    //     return Redirect::to('client/student/' . $studentId . '/program/' . $clientProgramId)->withSuccess('Follow Up plan has been created');
    // }

    // public function update(Request $request)
    // {
    //     $followupId = $request->route('followup');
    //     $notes = $request->new_notes;

    //     $newDetails['status'] = 0;

    //     if ($request->mark == "true") {

    //         $newDetails['status'] = 1;
    //         $newDetails['notes'] = $notes;

    //     }

    //     DB::beginTransaction();
    //     try {

            
    //         $this->followupRepository->updateFollowup($followupId, $newDetails);
                
    //         DB::commit();           

    //     } catch (Exception $e) {

    //         DB::rollBack();
    //         Log::error('Store followup plan failed : ' . $e->getMessage());
    //         // if ($request->ajax())
    //             return response()->json(['success' => false]);
            
    //         // return Redirect::back()->withError('Failed to update followup plan');

    //     }

    //     // if ($request->ajax())
    //         return response()->json(['success' => true]);

    //     // return Redirect::to('/dashboard')->withSuccess('Follow up plan has been updated');

    // }

    public function destroy(Request $request)
    {
        $studentId = $request->route('student');
        $clientProgramId = $request->route('program');
        $planId = $request->route('followup');

        DB::beginTransaction();
        try {

            $this->followupRepository->deleteFollowup($planId);
            DB::commit();           

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete followup plan failed : ' . $e->getMessage());
            return Redirect::to('client/student/' . $studentId . '/program/' . $clientProgramId)->withError('Failed to delete followup plan');

        }

        return Redirect::to('client/student/' . $studentId . '/program/' . $clientProgramId)->withSuccess('Follow Up plan has been deleted');

    }
}

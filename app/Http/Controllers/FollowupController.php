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

    # follow up plan for client program
    public function store(StoreFollowupRequest $request)
    {
        $studentId = $request->route('student');
        $clientProgramId = $request->route('program');

        $followupDetails = $request->only([
            'followup_date',
            'notes',
        ]);

        DB::beginTransaction();
        try {

            $this->followupRepository->createFollowup(['clientprog_id' => $clientProgramId] + $followupDetails);
            DB::commit();           

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store followup plan failed : ' . $e->getMessage());
            return Redirect::to('client/student/' . $studentId . '/program/' . $clientProgramId)->withError('Failed to add followup plan');

        }

        return Redirect::to('client/student/' . $studentId . '/program/' . $clientProgramId)->withSuccess('Follow Up plan has been created');
    }

    public function update(Request $request)
    {
        $followupId = $request->route('followup');
        $notes = $request->new_notes;

        $newDetails['status'] = 0;

        if ($request->mark == "true") {

            $newDetails['status'] = 1;
            $newDetails['notes'] = $notes;

        }

        DB::beginTransaction();
        try {

            
            $this->followupRepository->updateFollowup($followupId, $newDetails);
                
            DB::commit();           

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store followup plan failed : ' . $e->getMessage());
            // if ($request->ajax())
                return response()->json(['success' => false]);
            
            // return Redirect::back()->withError('Failed to update followup plan');

        }

        // if ($request->ajax())
            return response()->json(['success' => true]);

        // return Redirect::to('/dashboard')->withSuccess('Follow up plan has been updated');

    }

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

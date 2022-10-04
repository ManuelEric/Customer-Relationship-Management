<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVolunteerRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\VolunteerRepositoryInterface;
use App\Models\Volunteer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class VolunteerController extends Controller
{
    use CreateCustomPrimaryKeyTrait;

    private VolunteerRepositoryInterface $volunteerRepository;

    public function __construct(VolunteerRepositoryInterface $volunteerRepository)
    {
        $this->volunteerRepository = $volunteerRepository;
    }

    public function index(): JsonResponse
    {
        return response()->json(
            [
                'data' => $this->volunteerRepository->getAllVolunteer()
            ]
        );
    }

    public function store(StoreVolunteerRequest $request)
    {
        $volunteerDetails = $request->only([
            'volunt_firstname',
            'volunt_lastname',
            'volunt_mail',
            'volunt_address',
            'volunt_phone',
            'volunt_graduatedfr',
            'volunt_major',
            'volunt_position',
        ]);
        
        $last_id = Volunteer::max('volunt_id');
        $volunteer_id_without_label = $this->remove_primarykey_label($last_id, 4);
        $volunteer_id_with_label = 'VLT-' . $this->add_digit($volunteer_id_without_label+1);

        DB::beginTransaction();
        try {

            $this->volunteerRepository->createVolunteer(['volunt_id' => $volunteer_id_with_label] + $volunteerDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store volunteer failed : ' . $e->getMessage());

        }

        return Redirect::to('volunteer');
    }

    public function create()
    {
        return view('form-volunteer');
    }

    public function edit(Request $request)
    {
        $volunteerId = $request->route('volunteer');

        # retrieve volunteer data by id
        $volunteer = $this->volunteerRepository->getVolunteerById($volunteerId);

        # put the link to update volunteer form below
        # example
        return view('form-volunteer')->with(
            [
                'volunteer' => $volunteer,
            ]
        );
    }

    public function update(StoreVolunteerRequest $request)
    {
        $volunteerDetails = $request->only([
            'volunt_firstname',
            'volunt_lastname',
            'volunt_mail',
            'volunt_address',
            'volunt_phone',
            'volunt_graduatedfr',
            'volunt_major',
            'volunt_position',
        ]);

        # retrieve vendor id from url
        $volunteerId = $request->route('volunteer');

        DB::beginTransaction();
        try {

            $this->volunteerRepository->updateVolunteer($volunteerId, $volunteerDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update volunteer failed : ' . $e->getMessage());

        }   
        
        return Redirect::to('volunteer');
    }

    public function destroy(Request $request)
    {
        $volunteerId = $request->route('volunteer');

        DB::beginTransaction();
        try {

            $this->volunteerRepository->deleteVolunteer($volunteerId);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete volunteer failed : ' . $e->getMessage());

        }

        return Redirect::to('volunteer');
    }
}

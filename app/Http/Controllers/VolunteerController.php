<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVolunteerRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\VolunteerRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
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
    private UniversityRepositoryInterface $universityRepository;

    public function __construct(VolunteerRepositoryInterface $volunteerRepository, UniversityRepositoryInterface $universityRepository)
    {
        $this->volunteerRepository = $volunteerRepository;
        $this->universityRepository = $universityRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->volunteerRepository->getAllVolunteerDataTables();
        }
        return view('pages.user.volunteer.index');
    }

    // public function data(): JsonResponse
    // {
    //     return $this->volunteerRepository->getAllVolunteerDataTables();
    // }

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
        $volunteer_id_with_label = 'VLT-' . $this->add_digit($volunteer_id_without_label + 1, 4);

        DB::beginTransaction();
        try {

            $this->volunteerRepository->createVolunteer(['volunt_id' => $volunteer_id_with_label] + $volunteerDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store volunteer failed : ' . $e->getMessage());
            return Redirect::to('user/volunteer')->withError('Failed to create a new volunteer');
        }

        return Redirect::to('user/volunteer')->withSuccess('Volunteer successfully created');
    }

    public function create()
    {
        $universities = $this->universityRepository->getAllUniversities();
        return view('pages.user.volunteer.form')->with(
            [
                'universities' => $universities
            ]
        );
    }

    public function edit(Request $request)
    {
        $volunteerId = $request->route('volunteer');

        # retrieve volunteer data by id
        $volunteer = $this->volunteerRepository->getVolunteerById($volunteerId);
        $universities = $this->universityRepository->getAllUniversities();

        # put the link to update volunteer form below
        # example
        return view('pages.user.volunteer.form')->with(
            [
                'volunteer' => $volunteer,
                'universities' => $universities
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
            return Redirect::to('user/volunteer')->withError('Failed to update a volunteer');
        }

        return Redirect::to('user/volunteer')->withSuccess('Volunteer successfully updated');
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
            return Redirect::to('user/volunteer')->withError('Failed to delete a volunteer');
        }

        return Redirect::to('user/volunteer')->withSuccess('Volunteer successfully deleted');
    }
}

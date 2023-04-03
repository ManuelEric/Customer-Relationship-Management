<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVolunteerRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\VolunteerRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Models\Volunteer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Traits\StandardizePhoneNumberTrait;


class VolunteerController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use StandardizePhoneNumberTrait;

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
            'volunt_cv',
            'volunt_bank_accname',
            'volunt_bank_accnumber',
            'volunt_nik',
            'volunt_idcard',
            'volunt_npwp_number',
            'volunt_npwp',
            'volunt_bpjs_kesehatan',
            'volunt_bpjs_ketenagakerjaan',
        ]);

        $volunteerDetails['volunt_phone'] = $this->setPhoneNumber($request->volunt_phone);

        $last_id = Volunteer::max('volunt_id');
        $volunteer_id_without_label = $this->remove_primarykey_label($last_id, 4);
        $volunteer_id_with_label = 'VLT-' . $this->add_digit($volunteer_id_without_label + 1, 4);

        $volunt_cv = $this->attachment($request->file('volunt_cv'), 'CV', $volunteer_id_with_label);
        $volunteerDetails['volunt_cv'] = $volunt_cv;

        $volunt_idcard = $this->attachment($request->file('volunt_idcard'), 'ID-CARD', $volunteer_id_with_label);
        $volunteerDetails['volunt_idcard'] = $volunt_idcard;

        $volunt_npwp = isset($volunteerDetails['volunt_npwp']) ? $this->attachment($request->file('volunt_npwp'), 'NPWP', $volunteer_id_with_label) : null;
        $volunteerDetails['volunt_npwp'] = isset($volunt_npwp) ? $volunt_npwp : null;

        $volunt_bpjs_kesehatan = isset($volunteerDetails['volunt_bpjs_kesehatan']) ? $this->attachment($request->file('volunt_bpjs_kesehatan'), 'BPJS-Kesehatan', $volunteer_id_with_label) : null;
        $volunteerDetails['volunt_bpjs_kesehatan'] = isset($volunt_bpjs_kesehatan) ? $volunt_bpjs_kesehatan : null;

        $volunt_bpjs_ketenagakerjaan = isset($volunteerDetails['volunt_bpjs_ketenagakerjaan']) ? $this->attachment($request->file('volunt_bpjs_ketenagakerjaan'), 'BPJS-Ketenagakerjaan', $volunteer_id_with_label) : null;
        $volunteerDetails['volunt_bpjs_ketenagakerjaan'] = isset($volunt_bpjs_ketenagakerjaan) ? $volunt_bpjs_ketenagakerjaan : null;

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
            'volunt_position', 'volunt_cv',
            'volunt_bank_accname',
            'volunt_bank_accnumber',
            'volunt_nik',
            'volunt_idcard',
            'volunt_npwp_number',
            'volunt_npwp',
            'volunt_bpjs_kesehatan',
            'volunt_bpjs_ketenagakerjaan',
        ]);

        $volunteerDetails['volunt_phone'] = $this->setPhoneNumber($request->volunt_phone);

        # retrieve vendor id from url
        $volunteerId = $request->route('volunteer');

        $volunteer = $this->volunteerRepository->getVolunteerById($volunteerId);

        isset($volunteerDetails['volunt_cv']) ? $volunt_cv = $this->attachment($request->file('volunt_cv'), 'CV', $volunteerId) : '';
        $volunteerDetails['volunt_cv'] = isset($volunt_cv) ? $volunt_cv : $volunteer->volunt_cv;

        isset($volunteerDetails['volunt_idcard']) ? $volunt_idcard = $this->attachment($request->file('volunt_idcard'), 'ID-CARD', $volunteerId) : '';
        $volunteerDetails['volunt_idcard'] = isset($volunt_idcard) ? $volunt_idcard : $volunteer->volunt_idcard;

        isset($volunteerDetails['volunt_npwp']) ? $volunt_npwp = $this->attachment($request->file('volunt_npwp'), 'NPWP', $volunteerId) : '';
        $volunteerDetails['volunt_npwp'] = isset($volunt_npwp) ? $volunt_npwp : $volunteer->volunt_npwp;

        isset($volunteerDetails['volunt_bpjs_kesehatan']) ? $volunt_bpjs_kesehatan = $this->attachment($request->file('volunt_bpjs_kesehatan'), 'BPJS-Kesehatan', $volunteerId) : '';
        $volunteerDetails['volunt_bpjs_kesehatan'] = isset($volunt_bpjs_kesehatan) ? $volunt_bpjs_kesehatan : $volunteer->volunt_bpjs_kesehatan;

        isset($volunteerDetails['volunt_bpjs_ketenagakerjaan']) ? $volunt_bpjs_ketenagakerjaan = $this->attachment($request->file('volunt_bpjs_ketenagakerjaan'), 'BPJS-Ketenagakerjaan', $volunteerId) : '';
        $volunteerDetails['volunt_bpjs_ketenagakerjaan'] = isset($volunt_bpjs_ketenagakerjaan) ? $volunt_bpjs_ketenagakerjaan : $volunteer->volunt_bpjs_ketenagakerjaan;

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
        $path = 'attachment/volunteer_attach/' . $volunteerId . '/';

        DB::beginTransaction();
        try {

            if ($this->volunteerRepository->deleteVolunteer($volunteerId)) {
                if (File::exists($path)) {
                    File::deleteDirectory($path);
                }
            }

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete volunteer failed : ' . $e->getMessage());
            return Redirect::to('user/volunteer')->withError('Failed to delete a volunteer');
        }

        return Redirect::to('user/volunteer')->withSuccess('Volunteer successfully deleted');
    }

    public function updateStatus(Request $request)
    {
        $volunteerId = $request->route('volunteer');
        $newStatus = $request->route('status');

        # validate status
        if (!in_array($newStatus, [0, 1])) {

            return response()->json(
                [
                    'success' => false,
                    'message' => "Status is invalid"
                ]
            );
        }

        DB::beginTransaction();
        try {

            $this->volunteerRepository->updateActiveStatus($volunteerId, $newStatus);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update active status volunteer failed : ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ]
            );
        }

        return response()->json(
            [
                'success' => true,
                'message' => "Status has been updated",
            ]
        );
    }

    protected function attachment($file, $column, $volunt_id)
    {
        $file_name = $volunt_id . '_' . $column;
        $extension = $file->getClientOriginalExtension();
        $file_location = 'attachment/volunteer_attach/' . $volunt_id . '/';
        $attach = $file_name . '.' . $extension;
        $file->move($file_location, $file_name . '.' . $extension);

        return $attach;
    }
}

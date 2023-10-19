<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVolunteerRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\VolunteerRepositoryInterface;
use App\Interfaces\UniversityRepositoryInterface;
use App\Interfaces\MajorRepositoryInterface;
use App\Interfaces\PositionRepositoryInterface;
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
use Illuminate\Support\Facades\Auth;

class VolunteerController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use StandardizePhoneNumberTrait;
    use LoggingTrait;

    private VolunteerRepositoryInterface $volunteerRepository;
    private UniversityRepositoryInterface $universityRepository;
    private MajorRepositoryInterface $majorRepository;
    private PositionRepositoryInterface $positionRepository;

    public function __construct(VolunteerRepositoryInterface $volunteerRepository, UniversityRepositoryInterface $universityRepository, MajorRepositoryInterface $majorRepository, PositionRepositoryInterface $positionRepository)
    {
        $this->volunteerRepository = $volunteerRepository;
        $this->universityRepository = $universityRepository;
        $this->majorRepository = $majorRepository;
        $this->positionRepository = $positionRepository;
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
            'volunt_cv',
            'volunt_bank_accname',
            'volunt_bank_accnumber',
            'volunt_nik',
            'volunt_idcard',
            'volunt_npwp_number',
            'volunt_npwp',
            'health_insurance',
            'empl_insurance',
            'volunt_graduatedfr',
            'volunt_major',
            'volunt_position'
        ]);

        $volunteerDetails['univ_id'] = $volunteerDetails['volunt_graduatedfr'];
        $volunteerDetails['major_id'] = $volunteerDetails['volunt_major'];
        $volunteerDetails['position_id'] = $volunteerDetails['volunt_position'];
        unset($volunteerDetails['volunt_graduatedfr']);
        unset($volunteerDetails['volunt_major']);
        unset($volunteerDetails['volunt_position']);

        $volunt_name = [
            'first_name' => $volunteerDetails['volunt_firstname'],
            'last_name' => $volunteerDetails['volunt_lastname'],
        ];

        $volunteerDetails['volunt_phone'] = $this->setPhoneNumber($request->volunt_phone);

        $last_id = Volunteer::max('volunt_id');
        $volunteer_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 4) : '0000';
        $volunteer_id_with_label = 'VLT-' . $this->add_digit($volunteer_id_without_label + 1, 4);

        $volunt_cv = $this->attachment($volunt_name, $request->file('volunt_cv'), 'CV-', $volunteer_id_with_label);
        $volunteerDetails['volunt_cv'] = $volunt_cv;

        $volunt_idcard = $this->attachment($volunt_name, $request->file('volunt_idcard'), 'ID-', $volunteer_id_with_label);
        $volunteerDetails['volunt_idcard'] = $volunt_idcard;

        $volunt_npwp = isset($volunteerDetails['volunt_npwp']) ? $this->attachment($volunt_name, $request->file('volunt_npwp'), 'TAX-', $volunteer_id_with_label) : null;
        $volunteerDetails['volunt_npwp'] = isset($volunt_npwp) ? $volunt_npwp : null;

        $health_insurance = isset($volunteerDetails['health_insurance']) ? $this->attachment($volunt_name, $request->file('health_insurance'), 'HI-', $volunteer_id_with_label) : null;
        $volunteerDetails['health_insurance'] = isset($health_insurance) ? $health_insurance : null;

        $empl_insurance = isset($volunteerDetails['empl_insurance']) ? $this->attachment($volunt_name, $request->file('empl_insurance'), 'EI-', $volunteer_id_with_label) : null;
        $volunteerDetails['empl_insurance'] = isset($empl_insurance) ? $empl_insurance : null;

        DB::beginTransaction();
        try {

            $newVolunt = $this->volunteerRepository->createVolunteer(['volunt_id' => $volunteer_id_with_label] + $volunteerDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store volunteer failed : ' . $e->getMessage());
            return Redirect::to('user/volunteer')->withError('Failed to create a new volunteer');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Volunteer', Auth::user()->first_name . ' '. Auth::user()->last_name, $newVolunt);

        return Redirect::to('user/volunteer')->withSuccess('Volunteer successfully created');
    }

    public function create()
    {
        $universities = $this->universityRepository->getAllUniversities();
        $majors = $this->majorRepository->getAllMajors();
        $positions = $this->positionRepository->getAllPositions();
        return view('pages.user.volunteer.form')->with(
            [
                'universities' => $universities,
                'majors' => $majors,
                'positions' => $positions,
                'edit' => false,
            ]
        );
    }

    public function edit(Request $request)
    {
        $volunteerId = $request->route('volunteer');

        # retrieve volunteer data by id
        $volunteer = $this->volunteerRepository->getVolunteerById($volunteerId);
        $universities = $this->universityRepository->getAllUniversities();
        $majors = $this->majorRepository->getAllMajors();
        $positions = $this->positionRepository->getAllPositions();

        
        # put the link to update volunteer form below
        # example
        return view('pages.user.volunteer.form')->with(
            [
                'volunteer' => $volunteer,
                'universities' => $universities,
                'majors' => $majors,
                'positions' => $positions,
                'edit' => true,
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
            'volunt_cv',
            'volunt_bank_accname',
            'volunt_bank_accnumber',
            'volunt_nik',
            'volunt_idcard',
            'volunt_npwp_number',
            'volunt_npwp',
            'health_insurance',
            'empl_insurance',
        ]);

        $volunteerDetails['univ_id'] = $volunteerDetails['volunt_graduatedfr'];
        $volunteerDetails['major_id'] = $volunteerDetails['volunt_major'];
        $volunteerDetails['position_id'] = $volunteerDetails['volunt_position'];
        unset($volunteerDetails['volunt_graduatedfr']);
        unset($volunteerDetails['volunt_major']);
        unset($volunteerDetails['volunt_position']);

        $volunteerDetails['volunt_phone'] = $this->setPhoneNumber($request->volunt_phone);

        $volunt_name = [
            'first_name' => $volunteerDetails['volunt_firstname'],
            'last_name' => $volunteerDetails['volunt_lastname'],
        ];

        # retrieve vendor id from url
        $volunteerId = $request->route('volunteer');

        $volunteer = $this->volunteerRepository->getVolunteerById($volunteerId);

        isset($volunteerDetails['volunt_cv']) ? $volunt_cv = $this->attachment($volunt_name, $request->file('volunt_cv'), 'CV-', $volunteerId) : '';
        $volunteerDetails['volunt_cv'] = isset($volunt_cv) ? $volunt_cv : $volunteer->volunt_cv;

        isset($volunteerDetails['volunt_idcard']) ? $volunt_idcard = $this->attachment($volunt_name, $request->file('volunt_idcard'), 'ID-', $volunteerId) : '';
        $volunteerDetails['volunt_idcard'] = isset($volunt_idcard) ? $volunt_idcard : $volunteer->volunt_idcard;

        isset($volunteerDetails['volunt_npwp']) ? $volunt_npwp = $this->attachment($volunt_name, $request->file('volunt_npwp'), 'TAX-', $volunteerId) : '';
        $volunteerDetails['volunt_npwp'] = isset($volunt_npwp) ? $volunt_npwp : $volunteer->volunt_npwp;

        isset($volunteerDetails['health_insurance']) ? $health_insurance = $this->attachment($volunt_name, $request->file('health_insurance'), 'HI-', $volunteerId) : '';
        $volunteerDetails['health_insurance'] = isset($health_insurance) ? $health_insurance : $volunteer->health_insurance;

        isset($volunteerDetails['empl_insurance']) ? $empl_insurance = $this->attachment($volunt_name, $request->file('empl_insurance'), 'EI-', $volunteerId) : '';
        $volunteerDetails['empl_insurance'] = isset($empl_insurance) ? $empl_insurance : $volunteer->empl_insurance;

        DB::beginTransaction();
        try {

            $this->volunteerRepository->updateVolunteer($volunteerId, $volunteerDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update volunteer failed : ' . $e->getMessage());
            return Redirect::to('user/volunteer')->withError('Failed to update a volunteer');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Volunteer', Auth::user()->first_name . ' '. Auth::user()->last_name, $volunteerDetails, $volunteer);

        return Redirect::to('user/volunteer')->withSuccess('Volunteer successfully updated');
    }

    public function download(Request $request)
    {
        $volunteerId = $request->route('volunteer');

        # retrieve volunteer data by id
        $volunteer = $this->volunteerRepository->getVolunteerById($volunteerId);

        $path = 'public/uploaded_file/volunteer/' . $volunteerId . '/';

        switch ($request->route('filetype')) {

            case "CV":
                $file = Storage::disk('local')->get($path . $volunteer->volunt_cv);
                $extension = pathinfo(storage_path($path . $volunteer->volunt_cv), PATHINFO_EXTENSION);
                break;

            case "ID":
                $file = Storage::disk('local')->get($path . $volunteer->volunt_idcard);
                $extension = pathinfo(storage_path($path . $volunteer->volunt_idcard), PATHINFO_EXTENSION);
                break;

            case "TX":
                $file = Storage::disk('local')->get($path . $volunteer->volunt_npwp);
                $extension = pathinfo(storage_path($path . $volunteer->volunt_npwp), PATHINFO_EXTENSION);
                break;

            case "HI":
                $file = Storage::disk('local')->get($path . $volunteer->health_insurance);
                $extension = pathinfo(storage_path($path . $volunteer->health_insurance), PATHINFO_EXTENSION);
                break;

            case "EI":
                $file = Storage::disk('local')->get($path . $volunteer->empl_insurance);
                $extension = pathinfo(storage_path($path . $volunteer->empl_insurance), PATHINFO_EXTENSION);
                break;
        }

        if ($extension == 'pdf') {
            return response($file)->header('Content-Type', 'application/pdf');
        } else {
            return response($file)->header('Content-Type', 'image/' . $extension);
        }

        # Download success
        # create log success
        $this->logSuccess('download', null, 'Volunteer', Auth::user()->first_name . ' '. Auth::user()->last_name, ['volunteer_id' => $volunteerId, 'file_type' => $request->route('filetype')]);

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

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Volunteer', Auth::user()->first_name . ' '. Auth::user()->last_name, ['volunteer_id' => $volunteerId]);

        return Redirect::to('user/volunteer')->withSuccess('Volunteer successfully deleted');
    }

    public function changeStatus(Request $request)
    {
        $volunteerId = $request->route('volunteer');
        $volunteer = $this->volunteerRepository->getVolunteerById($volunteerId);
        $data = $request->params;
        $status = $data['new_status'];
        $newStatus = $status == "activate" ? 1 : 0;


        DB::beginTransaction();
        try {

            # update on users table
            $this->volunteerRepository->updateActiveStatus($volunteerId, $newStatus);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error(ucfirst($status) . ' ' . $volunteer->firstname . ' ' . $volunteer->lastname . ' failed : ' . $e->getMessage());
            return response()->json(['message' => 'Failed to ' . $status . ' ' . $volunteer->firstname . ' ' . $volunteer->lastname], 422);
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Status Volunteer', Auth::user()->first_name . ' '. Auth::user()->last_name, $newStatus, ['volunteer_id' => $volunteerId]);

        return response()->json(['message' => ucwords($volunteer->firstname . ' ' . $volunteer->lastname) . ' has been ' . $status], 200);
    }

    protected function attachment(array $volunt_name, $file, $column, $volunt_id)
    {
        $file_name = $column . str_replace(' ', '_', $volunt_name['first_name'] . '_' . $volunt_name['last_name']);
        $extension = $file->getClientOriginalExtension();
        // $file_location = 'attachment/volunteer_attach/' . $volunt_id . '/';
        $attach = $file_name . '.' . $extension;
        $file->storeAs('public/uploaded_file/volunteer/' . $volunt_id, $file_name . '.' . $extension);

        # Upload success
        # create log success
        $this->logSuccess('upload', null, 'Attachment Volunteer', Auth::user()->first_name . ' '. Auth::user()->last_name, ['filename' => $file_name]);

        return $attach;
    }
}

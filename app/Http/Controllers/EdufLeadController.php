<?php

namespace App\Http\Controllers;

use App\Actions\EdufLeads\CreateEdufLeadAction;
use App\Actions\EdufLeads\DeleteEdufLeadAction;
use App\Actions\EdufLeads\UpdateEdufLeadAction;
use App\Http\Requests\StoreEdufairRequest;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EdufReviewRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\AgendaSpeakerRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class EdufLeadController extends Controller
{
    use LoggingTrait, StandardizePhoneNumberTrait;

    private EdufLeadRepositoryInterface $edufLeadRepository;
    private SchoolRepositoryInterface $schoolRepository;
    private CorporateRepositoryInterface $corporateRepository;
    private UserRepositoryInterface $userRepository;
    private EdufReviewRepositoryInterface $edufReviewRepository;
    private AgendaSpeakerRepositoryInterface $agendaSpeakerRepository;


    public function __construct(EdufLeadRepositoryInterface $edufLeadRepository, SchoolRepositoryInterface $schoolRepository, CorporateRepositoryInterface $corporateRepository, UserRepositoryInterface $userRepository, EdufReviewRepositoryInterface $edufReviewRepository, AgendaSpeakerRepositoryInterface $agendaSpeakerRepository)
    {
        $this->edufLeadRepository = $edufLeadRepository;
        $this->schoolRepository = $schoolRepository;
        $this->corporateRepository = $corporateRepository;
        $this->userRepository = $userRepository;
        $this->edufReviewRepository = $edufReviewRepository;
        $this->agendaSpeakerRepository = $agendaSpeakerRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->edufLeadRepository->getAllEdufairLeadDataTables();
        }

        return view('pages.master.edufair.index');
    }

    public function create()
    {
        $schools = $this->schoolRepository->getAllSchools();
        $corporates = $this->corporateRepository->getAllCorporate();
        $user_from_client_department = $this->userRepository->getAllUsersByRole('Client');
        $user_from_biz_dev_department = $this->userRepository->getAllUsersByRole('BizDev');

        return view('pages.master.edufair.form')->with(
            [
                'schools' => $schools,
                'corporates' => $corporates,
                'internal_pic' => $user_from_client_department->merge($user_from_biz_dev_department)->sortBy('first_name'),
            ]
        );
    }

    public function store(StoreEdufairRequest $request, CreateEdufLeadAction $createEdufLeadAction)
    {
        $new_edufair_lead_details = $request->safe()->only([
            'organizer',
            'sch_id',
            'corp_id',
            'title',
            'location',
            'intr_pic',
            'ext_pic_name',
            'ext_pic_mail',
            'ext_pic_phone',
            'first_discussion_date',
            'last_discussion_date',
            'event_start',
            'event_end',
            'status',
            'notes'
        ]);

        DB::beginTransaction();
        try {

            $new_eduf_lead = $createEdufLeadAction->execute($new_edufair_lead_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store edufair failed : ' . $e->getMessage());
            return Redirect::to('master/edufair')->withError('Failed to create new edufair');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'External Edufair', Auth::user()->first_name . ' '. Auth::user()->last_name, $new_eduf_lead);

        return Redirect::to('master/edufair')->withSuccess('New Edufair successfully created');
    }

    public function update(StoreEdufairRequest $request, UpdateEdufLeadAction $updateEdufLeadAction)
    {
        $edufair_lead_details = $request->safe()->only([
            'organizer',
            'sch_id',
            'corp_id',
            'title',
            'location',
            'intr_pic',
            'ext_pic_name',
            'ext_pic_mail',
            'ext_pic_phone',
            'first_discussion_date',
            'last_discussion_date',
            'event_start',
            'event_end',
            'status',
            'notes'
        ]);

        $eduf_lead_id = $request->route('edufair');

        DB::beginTransaction();
        try {
            $old_eduf_lead = $this->edufLeadRepository->getEdufairLeadById($eduf_lead_id);

            $updateEdufLeadAction->execute($eduf_lead_id, $edufair_lead_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update edufair failed : ' . $e->getMessage());
            return Redirect::to('master/edufair/' . $eduf_lead_id)->withError('Failed to update edufair');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'External Edufair', Auth::user()->first_name . ' '. Auth::user()->last_name, $edufair_lead_details, $old_eduf_lead);

        return Redirect::to('master/edufair')->withSuccess('Edufair successfully updated');
    }

    public function show(Request $request)
    {
        $eduf_lead_id = $request->route('edufair');
        $eduf_lead = $this->edufLeadRepository->getEdufairLeadById($eduf_lead_id);
        $reviews = $this->edufReviewRepository->getAllEdufairReviewByEdufairId($eduf_lead_id);
        $review_form_data = [];
        if ($edufRId = $request->route('review'))
            $review_form_data = $this->edufReviewRepository->getEdufairReviewById($edufRId);

        $schools = $this->schoolRepository->getAllSchools();
        $corporates = $this->corporateRepository->getAllCorporate();
        $user_from_client_department = $this->userRepository->getAllUsersByRole('Client');
        $user_from_biz_dev_department = $this->userRepository->getAllUsersByRole('BizDev');
        $employees = $this->userRepository->getAllUsersByRole('Employee');
        $speakers = $this->agendaSpeakerRepository->getAllSpeakerByEdufair($eduf_lead_id);


        return view('pages.master.edufair.form')->with(
            [
                'edufair' => $eduf_lead,
                'reviews' => $reviews,
                'reviewFormData' => $review_form_data,
                'schools' => $schools,
                'corporates' => $corporates,
                'internal_pic' => $user_from_client_department->merge($user_from_biz_dev_department)->sortBy('first_name'),
                'employees' => $employees,
                'speakers' => $speakers,
            ]
        );
    }

    public function edit(Request $request)
    {
        $eduf_lead_id = $request->route('edufair');
        $eduf_lead = $this->edufLeadRepository->getEdufairLeadById($eduf_lead_id);
        $reviews = $this->edufReviewRepository->getAllEdufairReviewByEdufairId($eduf_lead_id);
        $review_form_data = [];
        if ($edufRId = $request->route('review'))
            $review_form_data = $this->edufReviewRepository->getEdufairReviewById($edufRId);

        $schools = $this->schoolRepository->getAllSchools();
        $corporates = $this->corporateRepository->getAllCorporate();
        $user_from_client_department = $this->userRepository->getAllUsersByRole('Client');
        $user_from_biz_dev_department = $this->userRepository->getAllUsersByRole('BizDev');

        return view('pages.master.edufair.form')->with(
            [
                'edit' => true,
                'edufair' => $eduf_lead,
                'reviews' => $reviews,
                'reviewFormData' => $review_form_data,
                'schools' => $schools,
                'corporates' => $corporates,
                'internal_pic' => $user_from_client_department->merge($user_from_biz_dev_department)->sortBy('first_name'),
            ]
        );
    }

    public function destroy(Request $request, DeleteEdufLeadAction $deleteEdufLeadAction)
    {
        $eduf_lead_id = $request->route('edufair');
        $eduf_lead = $this->edufLeadRepository->getEdufairLeadById($eduf_lead_id);

        DB::beginTransaction();
        try {

            $deleteEdufLeadAction->execute($eduf_lead_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete edufair lead failed : ' . $e->getMessage());
            return Redirect::to('master/edufair')->withError('Failed to delete edufair');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'External Edufair', Auth::user()->first_name . ' '. Auth::user()->last_name, $eduf_lead);

        return Redirect::to('master/edufair')->withSuccess('Edufair successfully deleted');
    }
}

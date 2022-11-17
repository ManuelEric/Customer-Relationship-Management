<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEdufairRequest;
use App\Interfaces\CorporateRepositoryInterface;
use App\Interfaces\EdufLeadRepositoryInterface;
use App\Interfaces\EdufReviewRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class EdufLeadController extends Controller
{

    private EdufLeadRepositoryInterface $edufLeadRepository;
    private SchoolRepositoryInterface $schoolRepository;
    private CorporateRepositoryInterface $corporateRepository;
    private UserRepositoryInterface $userRepository;
    private EdufReviewRepositoryInterface $edufReviewRepository;

    public function __construct(EdufLeadRepositoryInterface $edufLeadRepository, SchoolRepositoryInterface $schoolRepository, CorporateRepositoryInterface $corporateRepository, UserRepositoryInterface $userRepository, EdufReviewRepositoryInterface $edufReviewRepository)
    {
        $this->edufLeadRepository = $edufLeadRepository;
        $this->schoolRepository = $schoolRepository;
        $this->corporateRepository = $corporateRepository;
        $this->userRepository = $userRepository;
        $this->edufReviewRepository = $edufReviewRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->edufLeadRepository->getAllEdufairLeadDataTables();
        }

        return view('pages.instance.edufair.index');
    }

    public function create()
    {
        $schools = $this->schoolRepository->getAllSchools();
        $corporates = $this->corporateRepository->getAllCorporate();
        $userFromClientDepartment = $this->userRepository->getAllUsersByRole('Client');
        $userFromBizDevDepartment = $this->userRepository->getAllUsersByRole('BizDev');

        return view('pages.instance.edufair.form')->with(
            [
                'schools' => $schools,
                'corporates' => $corporates,
                'internal_pic' => $userFromClientDepartment->merge($userFromBizDevDepartment)->sortBy('first_name'),
            ]
        );
    }

    public function store(StoreEdufairRequest $request)
    {
        $edufairLeadDetails = $request->only([
            'organizer',
            'sch_id',
            'corp_id',
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

            $this->edufLeadRepository->createEdufairLead($edufairLeadDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store edufair failed : ' . $e->getMessage());
            return Redirect::to('instance/edufair')->withError('Failed to create new edufair');
        }

        return Redirect::to('instance/edufair')->withSuccess('New Edufair successfully created');
    }

    public function update(StoreEdufairRequest $request)
    {
        $edufairLeadDetails = $request->only([
            'organizer',
            'sch_id',
            'corp_id',
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

        $edufLeadId = $request->route('edufair');
        if ($request->organizer == "school")
            $edufairLeadDetails['corp_id'] = NULL;
        else
            $edufairLeadDetails['sch_id'] = NULL;

        unset($edufairLeadDetails['organizer']);

        DB::beginTransaction();
        try {

            $this->edufLeadRepository->updateEdufairLead($edufLeadId, $edufairLeadDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update edufair failed : ' . $e->getMessage());
            return Redirect::to('instance/edufair/create')->withError('Failed to update edufair');
        }

        return Redirect::to('instance/edufair')->withSuccess('Edufair successfully created');
    }

    public function show(Request $request)
    {
        $edufLeadId = $request->route('edufair');
        $edufLead = $this->edufLeadRepository->getEdufairLeadById($edufLeadId);
        $reviews = $this->edufReviewRepository->getAllEdufairReviewByEdufairId($edufLeadId);
        $reviewFormData = [];
        if ($edufRId = $request->route('review'))
            $reviewFormData = $this->edufReviewRepository->getEdufairReviewById($edufRId);

        $schools = $this->schoolRepository->getAllSchools();
        $corporates = $this->corporateRepository->getAllCorporate();
        $userFromClientDepartment = $this->userRepository->getAllUsersByRole('Client');
        $userFromBizDevDepartment = $this->userRepository->getAllUsersByRole('BizDev');

        return view('pages.instance.edufair.form')->with(
            [
                'edufair' => $edufLead,
                'reviews' => $reviews,
                'reviewFormData' => $reviewFormData,
                'schools' => $schools,
                'corporates' => $corporates,
                'internal_pic' => $userFromClientDepartment->merge($userFromBizDevDepartment)->sortBy('first_name'),
            ]
        );
    }

    public function edit(Request $request)
    {
        $edufLeadId = $request->route('edufair');
        $edufLead = $this->edufLeadRepository->getEdufairLeadById($edufLeadId);
        $reviews = $this->edufReviewRepository->getAllEdufairReviewByEdufairId($edufLeadId);
        $reviewFormData = [];
        if ($edufRId = $request->route('review'))
            $reviewFormData = $this->edufReviewRepository->getEdufairReviewById($edufRId);

        $schools = $this->schoolRepository->getAllSchools();
        $corporates = $this->corporateRepository->getAllCorporate();
        $userFromClientDepartment = $this->userRepository->getAllUsersByRole('Client');
        $userFromBizDevDepartment = $this->userRepository->getAllUsersByRole('BizDev');

        return view('pages.instance.edufair.form')->with(
            [
                'edit' => true,
                'edufair' => $edufLead,
                'reviews' => $reviews,
                'reviewFormData' => $reviewFormData,
                'schools' => $schools,
                'corporates' => $corporates,
                'internal_pic' => $userFromClientDepartment->merge($userFromBizDevDepartment)->sortBy('first_name'),
            ]
        );
    }

    public function destroy(Request $request)
    {
        $id = $request->route('edufair');

        DB::beginTransaction();
        try {

            $this->edufLeadRepository->deleteEdufairLead($id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete edufair lead failed : ' . $e->getMessage());
            return Redirect::to('instance/edufair')->withError('Failed to delete edufair');
        }

        return Redirect::to('instance/edufair')->withSuccess('Edufair successfully deleted');
    }
}
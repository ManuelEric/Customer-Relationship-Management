<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEdufairRequest;
use App\Http\Traits\LoggingTrait;
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
    use LoggingTrait;

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
        $userFromClientDepartment = $this->userRepository->getAllUsersByRole('Client');
        $userFromBizDevDepartment = $this->userRepository->getAllUsersByRole('BizDev');

        return view('pages.master.edufair.form')->with(
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

            $ext_pic_phone = $request->ext_pic_phone;

            switch (substr($ext_pic_phone, 0, 1)) {

                case 0:
                    $ext_pic_phone = "+62" . substr($ext_pic_phone, 1);
                    break;

                case 6:
                    $ext_pic_phone = "+" . $ext_pic_phone;
                    break;

                case "+":
                    $ext_pic_phone = $ext_pic_phone;
                    break;

                default:
                    $ext_pic_phone = "+62" . $ext_pic_phone;
            }

            unset($edufairLeadDetails['ext_pic_phone']); # remove the phone number that hasn't been updated into +62
            $edufairLeadDetails['ext_pic_phone'] = $ext_pic_phone; # add new phone number 


            $newEduf = $this->edufLeadRepository->createEdufairLead($edufairLeadDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store edufair failed : ' . $e->getMessage());
            return Redirect::to('master/edufair')->withError('Failed to create new edufair');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'External Edufair', Auth::user()->first_name . ' '. Auth::user()->last_name, $newEduf);

        return Redirect::to('master/edufair')->withSuccess('New Edufair successfully created');
    }

    public function update(StoreEdufairRequest $request)
    {
        $edufairLeadDetails = $request->only([
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

        $ext_pic_phone = $request->ext_pic_phone;

        switch (substr($ext_pic_phone, 0, 1)) {

            case 0:
                $ext_pic_phone = "+62" . substr($ext_pic_phone, 1);
                break;

            case 6:
                $ext_pic_phone = "+" . $ext_pic_phone;
                break;

            case "+":
                $ext_pic_phone = $ext_pic_phone;
                break;

            default:
                $ext_pic_phone = "+62" . $ext_pic_phone;
        }

        unset($edufairLeadDetails['ext_pic_phone']); # remove the phone number that hasn't been updated into +62
        $edufairLeadDetails['ext_pic_phone'] = $ext_pic_phone; # add new phone number 

        $edufLeadId = $request->route('edufair');

        $oldEdufLead = $this->edufLeadRepository->getEdufairLeadById($edufLeadId);

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
            return Redirect::to('master/edufair/' . $edufLeadId)->withError('Failed to update edufair');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'External Edufair', Auth::user()->first_name . ' '. Auth::user()->last_name, $edufairLeadDetails, $oldEdufLead);

        return Redirect::to('master/edufair')->withSuccess('Edufair successfully updated');
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
        $employees = $this->userRepository->getAllUsersByRole('Employee');
        $speakers = $this->agendaSpeakerRepository->getAllSpeakerByEdufair($edufLeadId);


        return view('pages.master.edufair.form')->with(
            [
                'edufair' => $edufLead,
                'reviews' => $reviews,
                'reviewFormData' => $reviewFormData,
                'schools' => $schools,
                'corporates' => $corporates,
                'internal_pic' => $userFromClientDepartment->merge($userFromBizDevDepartment)->sortBy('first_name'),
                'employees' => $employees,
                'speakers' => $speakers,
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

        return view('pages.master.edufair.form')->with(
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
        $edufLead = $this->edufLeadRepository->getEdufairLeadById($id);

        DB::beginTransaction();
        try {

            $this->edufLeadRepository->deleteEdufairLead($id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete edufair lead failed : ' . $e->getMessage());
            return Redirect::to('master/edufair')->withError('Failed to delete edufair');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'External Edufair', Auth::user()->first_name . ' '. Auth::user()->last_name, $edufLead);

        return Redirect::to('master/edufair')->withSuccess('Edufair successfully deleted');
    }
}

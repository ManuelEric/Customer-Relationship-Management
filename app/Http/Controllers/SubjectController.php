<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\SubjectRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SubjectController extends Controller
{
    use LoggingTrait;

    private SubjectRepositoryInterface $subjectRepository;

    public function __construct(SubjectRepositoryInterface $subjectRepository)
    {
        $this->subjectRepository = $subjectRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->subjectRepository->getAllSubjectsDataTables();
        }

        return view('pages.master.subject.index');
    }

    public function store(StoreSubjectRequest $request)
    {
        $subjectDetails = $request->only([
            'name',
        ]);

        DB::beginTransaction();
        try {

            $subjectCreated = $this->subjectRepository->createSubject($subjectDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store subject failed : ' . $e->getMessage());
            return Redirect::to('master/subject')->withError('Failed to create a new subject');
        }
        
        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Subject', Auth::user()->first_name . ' '. Auth::user()->last_name, $subjectCreated);

        return Redirect::to('master/subject')->withSuccess('Subject successfully created');
    }

    public function show(Request $request)
    {
        $subjectId = $request->route('subject');

        try {
            # retrieve subject
            $subject = $this->subjectRepository->getSubjectById($subjectId);
        } catch (Exception $e) {
            Log::error('Failed to show detail subject: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['subject' => $subject]);

    }

    public function update(StoreSubjectRequest $request)
    {
        $subjectDetails = $request->only([
            'name',
        ]);

        # retrieve vendor id from url
        $subjectId = $request->route('subject');
        $oldSubject = $this->subjectRepository->getSubjectById($subjectId);

        DB::beginTransaction();
        try {

            $this->subjectRepository->updateSubject($subjectId, $subjectDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update subject failed : ' . $e->getMessage());
            return Redirect::to('master/subject')->withError('Failed to update a subject');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Subject', Auth::user()->first_name . ' '. Auth::user()->last_name, $subjectDetails, $oldSubject);

        return Redirect::to('master/subject')->withSuccess('Subject successfully updated');
    }

    public function destroy(Request $request)
    {
        $subjectId = $request->route('subject');
        $subject = $this->subjectRepository->getSubjectById($subjectId);

        DB::beginTransaction();
        try {

            $this->subjectRepository->deleteSubject($subjectId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete subject failed : ' . $e->getMessage());
            return Redirect::to('master/subject')->withError('Failed to delete a subject');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Subject', Auth::user()->first_name . ' '. Auth::user()->last_name, $subject);

        return Redirect::to('master/subject')->withSuccess('Subject successfully deleted');
    }
}

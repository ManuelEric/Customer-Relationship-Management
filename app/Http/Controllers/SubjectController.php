<?php

namespace App\Http\Controllers;

use App\Actions\Subjects\CreateSubjectAction;
use App\Actions\Subjects\DeleteSubjectAction;
use App\Actions\Subjects\UpdateSubjectAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\SubjectRepositoryInterface;
use App\Services\Log\LogService;
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

    public function store(StoreSubjectRequest $request, CreateSubjectAction $createSubjectAction, LogService $log_service)
    {
        $new_subject_details = $request->safe()->only([
            'name',
        ]);

        DB::beginTransaction();
        try {

            $subject_created = $createSubjectAction->execute($new_subject_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_SUBJECT, $e->getMessage(), $e->getLine(), $e->getFile(), $new_subject_details);

            return Redirect::to('master/subject')->withError('Failed to create a new subject');
        }
        
        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_SUBJECT, 'New subject has been added', $subject_created->toArray());

        return Redirect::to('master/subject')->withSuccess('Subject successfully created');
    }

    public function show(Request $request)
    {
        $subject_id = $request->route('subject');

        try {
            # retrieve subject
            $subject = $this->subjectRepository->getSubjectById($subject_id);
        } catch (Exception $e) {
            
            Log::error('Failed to show detail subject: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['subject' => $subject]);

    }

    public function update(StoreSubjectRequest $request, UpdateSubjectAction $updateSubjectAction, LogService $log_service)
    {
        $new_subject_details = $request->safe()->only([
            'name',
        ]);

        # retrieve vendor id from url
        $subject_id = $request->route('subject');

        DB::beginTransaction();
        try {

            $updated_subject = $updateSubjectAction->execute($subject_id, $new_subject_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_SUBJECT, $e->getMessage(), $e->getLine(), $e->getFile(), $new_subject_details);

            return Redirect::to('master/subject')->withError('Failed to update a subject');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_SUBJECT, 'New subject has been added', $updated_subject->toArray());

        return Redirect::to('master/subject')->withSuccess('Subject successfully updated');
    }

    public function destroy(Request $request, DeleteSubjectAction $deleteSubjectAction, LogService $log_service)
    {
        $subject_id = $request->route('subject');
        $subject = $this->subjectRepository->getSubjectById($subject_id);

        DB::beginTransaction();
        try {

            $deleteSubjectAction->execute($subject_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_SUBJECT, $e->getMessage(), $e->getLine(), $e->getFile(), $subject->toArray());

            return Redirect::to('master/subject')->withError('Failed to delete a subject');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_SUBJECT, 'Subject has been deleted', $subject->toArray());

        return Redirect::to('master/subject')->withSuccess('Subject successfully deleted');
    }
}

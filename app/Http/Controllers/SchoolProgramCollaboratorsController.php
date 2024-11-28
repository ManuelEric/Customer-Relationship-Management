<?php

namespace App\Http\Controllers;

use App\Actions\ProgramCollaborators\CreateProgramCollaboratorAction;
use App\Actions\ProgramCollaborators\DeleteProgramCollaboratorAction;
use App\Enum\LogModule;
use App\Interfaces\SchoolProgramCollaboratorsRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class SchoolProgramCollaboratorsController extends Controller
{
    
    protected SchoolProgramCollaboratorsRepositoryInterface $schoolProgramCollaboratorsRepository;

    public function __construct(SchoolProgramCollaboratorsRepositoryInterface $schoolProgramCollaboratorsRepository)
    {
        $this->schoolProgramCollaboratorsRepository = $schoolProgramCollaboratorsRepository;
    }

    public function store(Request $request, CreateProgramCollaboratorAction $createProgramCollaboratorAction, LogService $log_service)
    {
        $school_id = $request->route('school');
        $schoolprog_id = $request->route('sch_prog');
        $collaborators = $request->route('collaborators');

        DB::beginTransaction();
        try {

           $added_collaborators = $createProgramCollaboratorAction->execute($request, $collaborators, $schoolprog_id, $this->schoolProgramCollaboratorsRepository);

            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();

            $log_service->createErrorLog(LogModule::STORE_SCHOOL_PROGRAM_COLLABORATOR, $e->getMessage(), $e->getLine(), $e->getFile(), $request->all());
            return Redirect::back()->withError('Failed to store '.$collaborators.' collaborators. Please try again.');

        }

        $log_service->createSuccessLog(LogModule::STORE_SCHOOL_PROGRAM_COLLABORATOR, 'New school program collaborator has been added', $added_collaborators);

        return Redirect::to('program/school/'.$school_id.'/detail/'.$schoolprog_id)->withSuccess($added_collaborators.' has been added as collaborator.');
    
    }

    public function destroy(Request $request, DeleteProgramCollaboratorAction $deleteProgramCollaboratorAction, LogService $log_service)
    {
        $sch_id = $request->route('school');
        $schprog_id = $request->route('sch_prog'); # same as corp prog id
        $collaborators = $request->route('collaborators');
        $collaborators_id = $request->route('collaborators_id');

        DB::beginTransaction();
        try {

            $removed_collaborators = $deleteProgramCollaboratorAction->execute($collaborators, $collaborators_id, $schprog_id, $this->schoolProgramCollaboratorsRepository);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();

            $log_service->createErrorLog(LogModule::DELETE_SCHOOL_PROGRAM_COLLABORATOR, $e->getMessage(), $e->getLine(), $e->getFile(), $removed_collaborators);

            return Redirect::back()->withError('Failed to delete '.$collaborators.' collaborators. Please try again.');

        }

        $log_service->createSuccessLog(LogModule::DELETE_SCHOOL_PROGRAM_COLLABORATOR, 'School program collaborator has been deleted', $removed_collaborators);

        return Redirect::to('program/school/'.$sch_id.'/detail/'.$schprog_id)->withSuccess($removed_collaborators.' has been removed as collaborator.');
    }
}

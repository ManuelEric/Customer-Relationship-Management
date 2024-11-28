<?php

namespace App\Http\Controllers;

use App\Actions\ProgramCollaborators\CreateProgramCollaboratorAction;
use App\Actions\ProgramCollaborators\DeleteProgramCollaboratorAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreSchoolCollaboratorsPartnerProgramRequest;
use App\Interfaces\PartnerProgramCollaboratorsRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PartnerProgramCollaboratorsController extends Controller
{

    protected PartnerProgramCollaboratorsRepositoryInterface $partnerProgramCollaboratorsRepository;

    public function __construct(PartnerProgramCollaboratorsRepositoryInterface $partnerProgramCollaboratorsRepository)
    {
        $this->partnerProgramCollaboratorsRepository = $partnerProgramCollaboratorsRepository;
    }

    public function store(StoreSchoolCollaboratorsPartnerProgramRequest $request, CreateProgramCollaboratorAction $createProgramCollaboratorAction, LogService $log_service)
    {
        $corp_id = $request->route('corp');
        $partnerprog_id = $request->route('corp_prog'); # same as corp prog id
        $collaborators = $request->route('collaborators');

        DB::beginTransaction();
        try {

            $added_collaborators = $createProgramCollaboratorAction->execute($request, $collaborators, $partnerprog_id, $this->partnerProgramCollaboratorsRepository);

            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();

            $log_service->createErrorLog(LogModule::STORE_PARTNER_PROGRAM_COLLABORATOR, $e->getMessage(), $e->getLine(), $e->getFile(), $request->all());
            return Redirect::back()->withError('Failed to store '.$collaborators.' collaborators. Please try again.');

        }

        $log_service->createSuccessLog(LogModule::STORE_PARTNER_PROGRAM_COLLABORATOR, 'New partner program collaborator has been added', $added_collaborators);

        return Redirect::to('program/corporate/'.$corp_id.'/detail/'.$partnerprog_id)->withSuccess($added_collaborators.' has been added as collaborator.');
    }

    public function destroy(Request $request, DeleteProgramCollaboratorAction $deleteProgramCollaboratorAction, LogService $log_service)
    {
        $corp_id = $request->route('corp');
        $partnerprog_id = $request->route('corp_prog'); # same as corp prog id
        $collaborators = $request->route('collaborators');
        $collaborators_id = $request->route('collaborators_id');

        DB::beginTransaction();
        try {

            $removed_collaborators = $deleteProgramCollaboratorAction->execute($collaborators, $collaborators_id, $partnerprog_id, $this->partnerProgramCollaboratorsRepository);
    
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_PARTNER_PROGRAM_COLLABORATOR, $e->getMessage(), $e->getLine(), $e->getFile(), $request->all());

            return Redirect::back()->withError('Failed to delete '.$collaborators.' collaborators. Please try again.');

        }

        $log_service->createSuccessLog(LogModule::STORE_PARTNER_PROGRAM_COLLABORATOR, 'New partner program collaborator has been added', $removed_collaborators);

        return Redirect::to('program/corporate/'.$corp_id.'/detail/'.$partnerprog_id)->withSuccess($removed_collaborators.' has been removed as collaborator.');
    }
}

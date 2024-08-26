<?php

namespace App\Http\Controllers;

use App\Interfaces\SchoolProgramCollaboratorsRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SchoolProgramCollaboratorsController extends Controller
{
    
    protected SchoolProgramCollaboratorsRepositoryInterface $schoolProgramCollaboratorsRepository;

    public function __construct(SchoolProgramCollaboratorsRepositoryInterface $schoolProgramCollaboratorsRepository)
    {
        $this->schoolProgramCollaboratorsRepository = $schoolProgramCollaboratorsRepository;
    }

    public function store(Request $request)
    {
        $schoolId = $request->route('school');
        $schoolprog_id = $request->route('sch_prog');
        $collaborators = $request->route('collaborators');

        DB::beginTransaction();
        try {

            switch ($collaborators) {

                case "school":
                    $choosen_school = $request->sch_id; # single data
                    $response = $this->schoolProgramCollaboratorsRepository->storeSchoolCollaborators($schoolprog_id, $choosen_school);
                    $added_collaborators = ucwords(strtolower($response->sch_name));
                    break;

                case "university":
                    $choosen_univ = $request->univ_id;
                    $response = $this->schoolProgramCollaboratorsRepository->storeUnivCollaborators($schoolprog_id, $choosen_univ);
                    $added_collaborators = ucwords(strtolower($response->univ_name));
                    break;

                case "partner":
                    $choosen_partner = $request->corp_id;
                    $response = $this->schoolProgramCollaboratorsRepository->storePartnerCollaborators($schoolprog_id, $choosen_partner);
                    $added_collaborators = ucwords(strtolower($response->corp_name));
                    break;

            }

            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::error("Failed to store ".$collaborators." collaborators from school program caused by ". $e->getMessage().' | Line '.$e->getLine());
            return Redirect::back()->withError('Failed to store '.$collaborators.' collaborators. Please try again.');

        }

        return Redirect::to('program/school/'.$schoolId.'/detail/'.$schoolprog_id)->withSuccess($added_collaborators.' has been added as collaborator.');
    
    }

    public function destroy(Request $request)
    {
        $schId = $request->route('school');
        $schprog_id = $request->route('sch_prog'); # same as corp prog id
        $collaborators = $request->route('collaborators');
        $collaboratorsId = $request->route('collaborators_id');

        DB::beginTransaction();
        try {

            switch ($collaborators) {

                case "school":
                    $response = $this->schoolProgramCollaboratorsRepository->deleteSchoolCollaborators($schprog_id, $collaboratorsId);
                    $removed_collaborators = ucwords(strtolower($response->sch_name));
                    break;

                case "university":
                    $response = $this->schoolProgramCollaboratorsRepository->deleteUnivCollaborators($schprog_id, $collaboratorsId);
                    $removed_collaborators = ucwords(strtolower($response->univ_name));
                    break;

                case "partner":
                    $response = $this->schoolProgramCollaboratorsRepository->deletePartnerCollaborators($schprog_id, $collaboratorsId);
                    $removed_collaborators = ucwords(strtolower($response->corp_name));
                    break;

            }
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error("Failed to remove ".$collaborators." collaborators from school program caused by ". $e->getMessage().' | Line '.$e->getLine());
            return Redirect::back()->withError('Failed to delete '.$collaborators.' collaborators. Please try again.');

        }

        return Redirect::to('program/school/'.$schId.'/detail/'.$schprog_id)->withSuccess($removed_collaborators.' has been removed as collaborator.');
    }
}

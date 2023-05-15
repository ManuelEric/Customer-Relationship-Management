<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSchoolCollaboratorsPartnerProgramRequest;
use App\Interfaces\PartnerProgramCollaboratorsRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class PartnerProgramCollaboratorsController extends Controller
{

    protected PartnerProgramCollaboratorsRepositoryInterface $partnerProgramCollaboratorsRepository;

    public function __construct(PartnerProgramCollaboratorsRepositoryInterface $partnerProgramCollaboratorsRepository)
    {
        $this->partnerProgramCollaboratorsRepository = $partnerProgramCollaboratorsRepository;
    }

    public function store(StoreSchoolCollaboratorsPartnerProgramRequest $request)
    {
        $corpId = $request->route('corp');
        $partnerprog_id = $request->route('corp_prog'); # same as corp prog id
        $collaborators = $request->route('collaborators');

        DB::beginTransaction();
        try {

            switch ($collaborators) {

                case "school":
                    $choosen_school = $request->sch_id; # single data
                    $response = $this->partnerProgramCollaboratorsRepository->storeSchoolCollaborators($partnerprog_id, $choosen_school);
                    $added_collaborators = ucwords(strtolower($response->sch_name));
                    break;

                case "university":
                    $choosen_univ = $request->univ_id;
                    $response = $this->partnerProgramCollaboratorsRepository->storeUnivCollaborators($partnerprog_id, $choosen_univ);
                    $added_collaborators = ucwords(strtolower($response->univ_name));
                    break;

                case "partner":
                    $choosen_partner = $request->corp_id;
                    $response = $this->partnerProgramCollaboratorsRepository->storePartnerCollaborators($partnerprog_id, $choosen_partner);
                    $added_collaborators = ucwords(strtolower($response->corp_name));
                    break;

            }

            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::error("Failed to store ".$collaborators." collaborators from partner program caused by ". $e->getMessage().' | Line '.$e->getLine());
            return Redirect::back()->withError('Failed to store '.$collaborators.' collaborators. Please try again.');

        }

        return Redirect::to('program/corporate/'.$corpId.'/detail/'.$partnerprog_id)->withSuccess($added_collaborators.' has been added as collaborator.');
    }

    public function destroy(Request $request)
    {
        $corpId = $request->route('corp');
        $partnerprog_id = $request->route('corp_prog'); # same as corp prog id
        $collaborators = $request->route('collaborators');
        $collaboratorsId = $request->route('collaborators_id');

        DB::beginTransaction();
        try {

            switch ($collaborators) {

                case "school":
                    $response = $this->partnerProgramCollaboratorsRepository->deleteSchoolCollaborators($partnerprog_id, $collaboratorsId);
                    $removed_collaborators = ucwords(strtolower($response->sch_name));
                    break;

                case "university":
                    $response = $this->partnerProgramCollaboratorsRepository->deleteUnivCollaborators($partnerprog_id, $collaboratorsId);
                    $removed_collaborators = ucwords(strtolower($response->univ_name));
                    break;

                case "partner":
                    $response = $this->partnerProgramCollaboratorsRepository->deletePartnerCollaborators($partnerprog_id, $collaboratorsId);
                    $removed_collaborators = ucwords(strtolower($response->corp_name));
                    break;

            }
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error("Failed to remove ".$collaborators." collaborators from partner program caused by ". $e->getMessage().' | Line '.$e->getLine());
            return Redirect::back()->withError('Failed to delete '.$collaborators.' collaborators. Please try again.');

        }

        return Redirect::to('program/corporate/'.$corpId.'/detail/'.$partnerprog_id)->withSuccess($removed_collaborators.' has been removed as collaborator.');
    }
}

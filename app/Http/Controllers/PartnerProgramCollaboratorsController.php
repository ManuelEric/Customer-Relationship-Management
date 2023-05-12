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
        $choosen_school = $request->sch_id; # single data
        $partnerprog_id = $request->route('corp_prog'); # same as corp prog id

        DB::beginTransaction();
        try {

            $this->partnerProgramCollaboratorsRepository->storeSchoolCollaborators($partnerprog_id, $choosen_school);
            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::error("Failed to store school collaborators from partner program caused by ". $e->getMessage().' | Line '.$e->getLine());
            return Redirect::back()->withError('Failed to store school collaborators. Please try again.');

        }
    }
}

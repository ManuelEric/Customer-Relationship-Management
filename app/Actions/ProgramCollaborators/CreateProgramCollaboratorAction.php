<?php

namespace App\Actions\ProgramCollaborators;

use Illuminate\Http\Request;

class CreateProgramCollaboratorAction
{

    public function execute(
        Request $request,
        $collaborators,
        $b2b_prog_id, #school_prog_id OR $partner_prog_id
        $repositoryProgramCollaborator #schoolProgramCollaboratorRepository OR partnerProgramCollaboratorRepository
    )
    {
        switch ($collaborators) {

            case "school":
                $choosen_school = $request->sch_id; # single data
                $response = $repositoryProgramCollaborator->storeSchoolCollaborators($b2b_prog_id, $choosen_school);
                $added_collaborators = ucwords(strtolower($response->sch_name));
                break;

            case "university":
                $choosen_univ = $request->univ_id;
                $response = $repositoryProgramCollaborator->storeUnivCollaborators($b2b_prog_id, $choosen_univ);
                $added_collaborators = ucwords(strtolower($response->univ_name));
                break;

            case "partner":
                $choosen_partner = $request->corp_id;
                $response = $repositoryProgramCollaborator->storePartnerCollaborators($b2b_prog_id, $choosen_partner);
                $added_collaborators = ucwords(strtolower($response->corp_name));
                break;
        }

        return $added_collaborators;
    }
}
<?php

namespace App\Actions\ProgramCollaborators;

use Illuminate\Http\Request;

class DeleteProgramCollaboratorAction
{

    public function execute(
        $collaborators,
        $collaborators_id,
        $b2b_prog_id, #school_prog_id OR $partner_prog_id
        $repositoryProgramCollaborator #schoolProgramCollaboratorRepository OR partnerProgramCollaboratorRepository
    )
    {
        switch ($collaborators) {

            case "school":
                $response = $repositoryProgramCollaborator->deleteSchoolCollaborators($b2b_prog_id, $collaborators_id);
                $removed_collaborators = ucwords(strtolower($response->sch_name));
                break;

            case "university":
                $response = $repositoryProgramCollaborator->deleteUnivCollaborators($b2b_prog_id, $collaborators_id);
                $removed_collaborators = ucwords(strtolower($response->univ_name));
                break;

            case "partner":
                $response = $repositoryProgramCollaborator->deletePartnerCollaborators($b2b_prog_id, $collaborators_id);
                $removed_collaborators = ucwords(strtolower($response->corp_name));
                break;

        }

        return $removed_collaborators;
    }
}
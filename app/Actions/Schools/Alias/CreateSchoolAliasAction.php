<?php

namespace App\Actions\Schools\Alias;

use App\Http\Requests\SchoolAliasRequest;
use App\Http\Requests\StoreSchoolRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Models\School;

class CreateSchoolAliasAction
{
    use CreateCustomPrimaryKeyTrait;
    private SchoolRepositoryInterface $schoolRepository;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, ClientRepositoryInterface $clientRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->clientRepository = $clientRepository;
    }

    public function execute(
        SchoolAliasRequest $request,
        Array $details,
    )
    {
        

        $created_new_alias = $this->schoolRepository->createNewAlias($details);

        # if is_convert is true
        # meaning that they store from form-alias on list school
        # meaning the raw school must be deleted
        if ($request->is_convert) {
            $rawSchId = $request->raw_sch_id;

            # getting all client that has deleted (soon) school
            $clientIds = $this->clientRepository->getClientBySchool($rawSchId)->pluck('id')->toArray();
            $this->clientRepository->updateClients($clientIds, ['sch_id' => $details['sch_id']]);

            # delete raw school
            $this->schoolRepository->deleteSchool($rawSchId);
        }
        # end process from convert to alias

        return $created_new_alias;
    }
}
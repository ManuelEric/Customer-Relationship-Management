<?php

namespace App\Actions\Schools\Raw;

use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;

class DeleteSchoolRawAction
{
    private SchoolRepositoryInterface $schoolRepository;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, ClientRepositoryInterface $clientRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->clientRepository = $clientRepository;
    }

    public function execute(
        bool $is_bulk = false,
        String $raw_school_id = null,
        Array $raw_school_ids = null,
    )
    {
        # Delete school
        if($is_bulk){
            $deleted_school = $this->schoolRepository->moveBulkToTrash($raw_school_ids);
        }else{
            $deleted_school = $this->schoolRepository->moveToTrash($raw_school_id);
        }
            
        # get all client that tagged with the school
        # and remove the school that being deleted
        $clients = $this->clientRepository->getClientBySchool($is_bulk ? $raw_school_ids : $raw_school_id)->pluck('id')->toArray();
        $this->clientRepository->updateClients($clients, ['sch_id' => NULL]);

        return $deleted_school;
    }
}
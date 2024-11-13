<?php

namespace App\Actions\Corporates;

use App\Interfaces\CorporateRepositoryInterface;

class DeleteCorporateAction
{
    private CorporateRepositoryInterface $corporateRepository;

    public function __construct(CorporateRepositoryInterface $corporateRepository)
    {
        $this->corporateRepository = $corporateRepository;
    }

    public function execute(
        String $corporate_id
    )
    {
        # Delete corporate
        $deleted_corporate = $this->corporateRepository->deleteCorporate($corporate_id);

        return $deleted_corporate;
    }
}
<?php

namespace App\Actions\Corporates;

use App\Interfaces\CorporateRepositoryInterface;

class UpdateCorporateAction
{
    private CorporateRepositoryInterface $corporateRepository;

    public function __construct(CorporateRepositoryInterface $corporateRepository)
    {
        $this->corporateRepository = $corporateRepository;
    }

    public function execute(
        Array $corporate_details
    )
    {
        # Update corporate
        $updated_corporate = $this->corporateRepository->updateCorporate($corporate_details['corp_id'], $corporate_details);

        return $updated_corporate;
    }
}
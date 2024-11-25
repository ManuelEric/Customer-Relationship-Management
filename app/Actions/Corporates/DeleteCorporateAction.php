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
        $corporate = $this->corporateRepository->getCorporateById($corporate_id);

        # Delete corporate
        $this->corporateRepository->deleteCorporate($corporate_id);

        return $corporate;
    }
}
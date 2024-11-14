<?php

namespace App\Actions\Corporates;

use App\Http\Requests\StoreCorporateRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\CorporateRepositoryInterface;
use App\Models\Corporate;
use Illuminate\Support\Facades\Hash;

class CreateCorporateAction
{
    use CreateCustomPrimaryKeyTrait;
    private CorporateRepositoryInterface $corporateRepository;

    public function __construct(CorporateRepositoryInterface $corporateRepository)
    {
        $this->corporateRepository = $corporateRepository;
    }

    public function execute(
        StoreCorporateRequest $request,
        Array $corporate_details
    )
    {
        $last_id = Corporate::max('corp_id');
        $corp_id_without_label =  $last_id ? $this->remove_primarykey_label($last_id, 5) : '0000';
        $corp_id_with_label = 'CORP-' . $this->add_digit($corp_id_without_label + 1, 4);
        $corporateDetails['corp_password'] = Hash::make($request->corp_password);

        # store new corporate
        $new_corporate = $this->corporateRepository->createCorporate(['corp_id' => $corp_id_with_label] + $corporate_details);

        return $new_corporate;
    }
}
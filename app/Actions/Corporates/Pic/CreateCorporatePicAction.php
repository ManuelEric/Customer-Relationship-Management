<?php

namespace App\Actions\Corporates\Pic;

use App\Http\Requests\StoreCorporatePicRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\CorporatePicRepositoryInterface;

class CreateCorporatePicAction
{
    use CreateCustomPrimaryKeyTrait, StandardizePhoneNumberTrait;
    private CorporatePicRepositoryInterface $corporatePicRepository;

    public function __construct(CorporatePicRepositoryInterface $corporatePicRepository)
    {
        $this->corporatePicRepository = $corporatePicRepository;
    }

    public function execute(
        StoreCorporatePicRequest $request,
        Array $pic_details
    )
    {
        unset($pic_details['pic_phone']);
        $pic_details['pic_phone'] = $this->tnSetPhoneNumber($request->pic_phone);

        $pic_details['corp_id'] = $request->route('corporate');


        # store new corporate pic
        $new_corporate_pic = $this->corporatePicRepository->createCorporatePic($pic_details);

        return $new_corporate_pic;
    }
}
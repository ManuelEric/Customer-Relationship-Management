<?php

namespace App\Actions\Corporates\Pic;

use App\Http\Requests\StoreCorporatePicRequest;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\CorporatePicRepositoryInterface;

class UpdateCorporatePicAction
{
    private CorporatePicRepositoryInterface $corporatePicRepository;

    use StandardizePhoneNumberTrait;

    public function __construct(CorporatePicRepositoryInterface $corporatePicRepository)
    {
        $this->corporatePicRepository = $corporatePicRepository;
    }

    public function execute(
        StoreCorporatePicRequest $request,
        int $pic_id,
        String $corp_id,
        Array $corporate_pic_details
    )
    {
        unset($corporate_pic_details['pic_phone']);
        $picDetails['pic_phone'] = $this->tnSetPhoneNumber($request->pic_phone);

        $corporate_pic_details['corp_id'] = $corp_id;

        # Update corporate pic
        $updated_corporate_pic = $this->corporatePicRepository->updateCorporatePic($pic_id, $corporate_pic_details);


        return $updated_corporate_pic;
    }
}
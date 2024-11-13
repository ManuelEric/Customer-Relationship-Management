<?php

namespace App\Actions\Corporates\Pic;

use App\Interfaces\CorporatePicRepositoryInterface;

class DeleteCorporatePicAction
{
    private CorporatePicRepositoryInterface $corporatePicRepository;

    public function __construct(CorporatePicRepositoryInterface $corporatePicRepository)
    {
        $this->corporatePicRepository = $corporatePicRepository;
    }

    public function execute(
        int $pic_id
    )
    {
        # Delete corporate pic
        $deleted_corporate_pic = $this->corporatePicRepository->deleteCorporatePic($pic_id);

        return $deleted_corporate_pic;
    }
}
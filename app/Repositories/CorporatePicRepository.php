<?php

namespace App\Repositories;

use App\Interfaces\CorporatePicRepositoryInterface;
use App\Models\CorporatePic;

class CorporatePicRepository implements CorporatePicRepositoryInterface
{
    public function getAllCorporatePicByCorporateId($corporateId)
    {
        return CorporatePic::where('corp_id', $corporateId)->get();
    }

    public function getCorporatePicById($picId)
    {
        return CorporatePic::find($picId);
    }

    public function deleteCorporatePic($picId)
    {
        return CorporatePic::destroy($picId);
    }

    public function createCorporatePic(array $picDetails)
    {
        return CorporatePic::create($picDetails);
    }

    public function updateCorporatePic($picId, array $newDetails)
    {
        return CorporatePic::whereId($picId)->update($newDetails);
    }
}
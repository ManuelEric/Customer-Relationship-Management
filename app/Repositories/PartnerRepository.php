<?php

namespace App\Repositories;

use App\Interfaces\PartnerRepositoryInterface;
use App\Models\Corporate;
use App\Models\Partner;
use DataTables;

class PartnerRepository implements PartnerRepositoryInterface 
{
    public function getAllPartnerDataTables()
    {
        return Datatables::eloquent(Partner::query())->rawColumns(['pt_address'])->make(true);
    }

    public function getAllPartner()
    {
        return Corporate::all();
    }

    public function getPartnerById($partnerId)
    {
        return Partner::find($partnerId);
    }

    public function deletePartner($partnerId)
    {
        return Partner::destroy($partnerId);
    }

    public function createPartner(array $partnerDetails)
    {
        return Partner::create($partnerDetails);
    }

    public function updatePartner($partnerId, array $newDetails)
    {
        return Partner::whereId($partnerId)->update($newDetails);
    }
}
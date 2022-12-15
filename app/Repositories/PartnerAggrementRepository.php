<?php

namespace App\Repositories;

use App\Interfaces\PartnerAggrementRepositoryInterface;
use App\Models\PartnerAggrement;
use DataTables;
use Illuminate\Support\Facades\DB;

class PartnerAggrementRepository implements PartnerAggrementRepositoryInterface
{

    public function getAllPartnerAggrementsByPartnerId($corpId)
    {
        return PartnerAggrement::where('corp_id', $corpId)->orderBy('id', 'asc')->get();
    }

    public function getPartnerAggrementById($partnerAggrementId)
    {
        return PartnerAggrement::find($partnerAggrementId);
    }

    public function deletePartnerAggrement($partnerAggrementId)
    {
        return PartnerAggrement::destroy($partnerAggrementId);
    }

    public function createPartnerAggrement(array $partnerAggrements)
    {
        return PartnerAggrement::create($partnerAggrements);
    }

    public function updatePartnerAggrement($partnerAggrementId, array $newAggrements)
    {
        return PartnerAggrement::find($partnerAggrementId)->update($newAggrements);
    }
}

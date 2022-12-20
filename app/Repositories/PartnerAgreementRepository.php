<?php

namespace App\Repositories;

use App\Interfaces\PartnerAgreementRepositoryInterface;
use App\Models\PartnerAgreement;
use DataTables;
use Illuminate\Support\Facades\DB;

class PartnerAgreementRepository implements PartnerAgreementRepositoryInterface
{

    public function getAllPartnerAgreementsByPartnerId($corpId)
    {
        return PartnerAgreement::where('corp_id', $corpId)->orderBy('id', 'asc')->get();
    }

    public function getPartnerAgreementById($partnerAgreementId)
    {
        return PartnerAgreement::find($partnerAgreementId);
    }

    public function deletePartnerAgreement($partnerAgreementId)
    {
        return PartnerAgreement::destroy($partnerAgreementId);
    }

    public function createPartnerAgreement(array $partnerAgreements)
    {
        return PartnerAgreement::create($partnerAgreements);
    }

    public function updatePartnerAgreement($partnerAgreementId, array $newAgreements)
    {
        return PartnerAgreement::find($partnerAgreementId)->update($newAgreements);
    }
}

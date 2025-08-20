<?php

namespace App\Repositories;

use App\Interfaces\PartnerAgreementRepositoryInterface;
use App\Models\PartnerAgreement;
use Carbon\Carbon;

class PartnerAgreementRepository implements PartnerAgreementRepositoryInterface
{
    public function getAllPartnerAgreementsByPartnerId($corpId)
    {
        return PartnerAgreement::where('corp_id', $corpId)->orderBy('id', 'asc')->get();
    }

    public function getPartnerAgreementByMonthly($monthYear, $type)
    {
        $today = Carbon::today()->format('Y-m-d');
        $date = Carbon::today()->addDays(7)->format('Y-m-d');

        $query = PartnerAgreement::whereBetween('end_date', [$today, $date]);

        return in_array($type, ['all', 'monthly']) ? $query->count() : ($type === 'list' ? $query->get() : null);

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

    public function rnGetExpiringPartnerAgreement(int $days)
    {
        return PartnerAgreement::whereRaw('DATEDIFF(end_date, now()) <= '.$days)->where('reminded', '<', 1)->get();
    }
}

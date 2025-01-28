<?php

namespace App\Repositories;

use App\Interfaces\PartnerAgreementRepositoryInterface;
use App\Models\PartnerAgreement;
use Carbon\Carbon;
use DataTables;
use Illuminate\Support\Facades\DB;

class PartnerAgreementRepository implements PartnerAgreementRepositoryInterface
{

    public function getAllPartnerAgreementsByPartnerId($corpId)
    {
        return PartnerAgreement::where('corp_id', $corpId)->orderBy('id', 'asc')->get();
    }

    public function getPartnerAgreementByMonthly($monthYear, $type)
    {
        $date = Carbon::today()->addDays(7)->format('Y-m-d');
        $today = Carbon::today()->format('Y-m-d');

        $query = PartnerAgreement::query();

        $query->whereBetween('end_date', [$today, $date]);

        switch ($type) {
            case 'all':
                return $query->count();
                break;
            case 'monthly':
                return $query->count();
                break;
            case 'list':
                return $query->get();
                break;
        }
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

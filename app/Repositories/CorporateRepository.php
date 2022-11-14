<?php

namespace App\Repositories;

use App\Interfaces\CorporateRepositoryInterface;
use App\Models\Corporate;
use DataTables;

class CorporateRepository implements CorporateRepositoryInterface 
{
    public function getAllCorporateDataTables()
    {
        return Datatables::eloquent(Corporate::query())->make(true);
    }

    public function getAllCorporate()
    {
        return Corporate::orderBy('corp_name', 'asc')->get();
    }

    public function getCorporateById($corporateId)
    {
        return Corporate::whereCorpId($corporateId);
    }

    public function deleteCorporate($corporateId)
    {
        return Corporate::whereCorpId($corporateId)->delete();
    }

    public function createCorporate(array $corporateDetails)
    {
        return Corporate::create($corporateDetails);
    }

    public function updateCorporate($corporateId, array $newDetails)
    {
        return Corporate::whereCorpId($corporateId)->update($newDetails);
    }

    public function cleaningCorporate()
    {
        Corporate::where('corp_industry', '=', '')->update(
            [
                'corp_industry' => null
            ]
        );

        Corporate::where('corp_mail', '=', '')->orWhere('corp_mail', '=', '-')->orWhere('corp_mail', '=', 'no email. contact it')->update(
            [
                'corp_industry' => null
            ]
        );

        Corporate::where('corp_phone', '=', '')->orWhere('corp_phone', '=', '-')->orWhere('corp_phone', '=', 'no contact')->update(
            [
                'corp_phone' => null
            ]
        );

        Corporate::where('corp_insta', '=', '')->update(
            [
                'corp_insta' => null
            ]
        );

        Corporate::where('corp_site', '=', '')->update(
            [
                'corp_site' => null
            ]
        );

        Corporate::where('corp_region', '=', '')->update(
            [
                'corp_region' => null
            ]
        );

        Corporate::where('corp_address', '=', '')->update(
            [
                'corp_address' => null
            ]
        );

        Corporate::where('corp_note', '=', '')->update(
            [
                'corp_note' => null
            ]
        );

        Corporate::where('corp_password', '=', '')->update(
            [
                'corp_password' => null
            ]
        );
    }
}
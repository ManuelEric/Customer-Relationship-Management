<?php

namespace App\Repositories;

use App\Interfaces\AssetRepositoryInterface;
use App\Models\Asset;
use App\Models\v1\Asset as CRMAsset;
use DataTables;
use Illuminate\Support\Facades\DB;

class AssetRepository implements AssetRepositoryInterface 
{
    public function getAllAssetsDataTables()
    {
        return Datatables::eloquent(Asset::query())->make(true);
    }

    public function getAllAssets()
    {
        return Asset::orderBy('asset_dateachieved', 'desc')->get();
    }

    public function getAssetById($assetId) 
    {
        return Asset::whereAssetId($assetId);
    }

    public function deleteAsset($assetId) 
    {
        Asset::destroy($assetId);
    }

    public function createAsset(array $assetDetails) 
    {
        return Asset::create($assetDetails);
    }

    public function updateAsset($assetId, array $newDetails) 
    {
        return tap(Asset::whereAssetId($assetId))->update($newDetails);
    }

    public function cleaningAsset()
    {
        Asset::where('asset_merktype', '=', '')->update(
            [
                'asset_merktype' => null
            ]
        );

        Asset::where('asset_dateachieved', '=', '0000-00-00')->update(
            [
                'asset_dateachieved' => null
            ]
        );

        Asset::where('asset_unit', '=', '')->update(
            [
                'asset_unit' => null
            ]
        );

        Asset::where('asset_notes', '=', '')->update(
            [
                'asset_notes' => null
            ]
        );

        Asset::where('asset_status', '=', '')->update(
            [
                'asset_status' => null
            ]
        );
    }

    # CRM
    public function getAssetFromV1()
    {
        return CRMAsset::select([
            'asset_id',
            'asset_name',
            'asset_amount',
            'asset_condition',
            DB::raw('(CASE
                WHEN asset_merktype = "" THEN NULL ELSE asset_merktype
            END) AS asset_merktype'),
            DB::raw('(CASE
                WHEN asset_dateachieved = "0000-00-00" THEN NULL ELSE asset_dateachieved
            END) AS asset_dateachieved'),
            DB::raw('(CASE
                WHEN asset_unit = "" THEN NULL ELSE asset_unit
            END) AS asset_unit'),
            DB::raw('(CASE
                WHEN asset_notes = "" THEN NULL ELSE asset_notes
            END) AS asset_notes'),
            DB::raw('(CASE
                WHEN asset_status = "" THEN NULL ELSE asset_status
            END) AS asset_status'),
        ])->get();
    }
}
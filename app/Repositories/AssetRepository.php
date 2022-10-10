<?php

namespace App\Repositories;

use App\Interfaces\AssetRepositoryInterface;
use App\Models\Asset;
use DataTables;

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
        return Asset::findOrFail($assetId);
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
        return Asset::whereAssetId($assetId)->update($newDetails);
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
}
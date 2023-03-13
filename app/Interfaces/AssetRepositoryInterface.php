<?php

namespace App\Interfaces;

interface AssetRepositoryInterface 
{
    public function getAllAssetsDataTables();
    public function getAllAssets();
    public function getAssetById($assetId);
    public function deleteAsset($assetId);
    public function createAsset(array $assetDetails);
    public function updateAsset($assetId, array $newDetails);
    public function cleaningAsset();

    # crm
    public function getAssetFromV1();
}
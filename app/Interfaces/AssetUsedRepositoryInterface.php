<?php

namespace App\Interfaces;

interface AssetUsedRepositoryInterface 
{
    public function getAllAssetUsedDataTables($assetId);
    public function createAssetUser($asset, $userId, array $assetDetails);
    public function updateAssetUsed($asset, $userId, array $newDetails);
}
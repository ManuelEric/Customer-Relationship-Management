<?php

namespace App\Interfaces;

interface AssetUsedRepositoryInterface 
{
    public function getAllAssetUsedDataTables($assetId);
    public function createAssetUsed(array $usedDetails);
    // public function updateAssetUsed(array $newDetails);
    public function deleteAssetUsed($assetId, $usedId);
}
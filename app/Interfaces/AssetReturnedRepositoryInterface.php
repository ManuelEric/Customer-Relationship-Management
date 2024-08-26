<?php

namespace App\Interfaces;

interface AssetReturnedRepositoryInterface 
{
    public function createAssetReturned(array $returnedDetails);
    public function deleteAssetReturned($assetId, $returnedId);
}
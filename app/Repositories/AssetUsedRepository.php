<?php

namespace App\Repositories;

use App\Interfaces\AssetUsedRepositoryInterface;
use App\Models\Asset;
use App\Models\User;
use DataTables;

class AssetUsedRepository implements AssetUsedRepositoryInterface 
{
    # assuming views using a table list
    public function getAllAssetUsedDataTables($assetId)
    {
        return Datatables::eloquent(User::whereHas('asset', function($query) use ($assetId) {
            $query->where('tbl_asset_used.asset_id', $assetId);
        }))->make(true);
    }

    public function createAssetUser($asset, $userId, array $assetDetails)
    {
        # save into asset used
        if ($asset->user()->attach($userId, $assetDetails)) {

            # update asset running stock 

            # get asset amount used
            foreach ($assetDetails as $detail) {
                
            }

        }

        
    }

    public function updateAssetUsed($asset, $userId, array $newDetails)
    {
        return $asset->user()->updateExistingPivot($userId, $newDetails, false);
    }
}
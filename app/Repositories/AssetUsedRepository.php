<?php

namespace App\Repositories;

use App\Interfaces\AssetUsedRepositoryInterface;
use App\Models\Asset;
use App\Models\pivot\AssetUsed;
use App\Models\User;
use DataTables;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AssetUsedRepository implements AssetUsedRepositoryInterface 
{
    # assuming views using a table list
    public function getAllAssetUsedDataTables($assetId)
    {
        return Datatables::eloquent(User::whereHas('asset', function($query) use ($assetId) {
            $query->where('tbl_asset_used.asset_id', $assetId);
        }))->make(true);
    }

    public function createAssetUsed(array $usedDetails)
    {
        $asset = Asset::whereAssetId($usedDetails['asset_id']);
        $userId = $usedDetails['user'];
        $usedDetails['created_at'] = Carbon::now();
        $usedDetails['updated_at'] = Carbon::now();

        # save into asset used
        $asset->userUsedAsset()->attach($userId, $usedDetails);

        # update asset running stock 
        $asset->asset_running_stock = $asset->asset_running_stock + $usedDetails['amount_used'];
        $asset->save();
        
    }

    // public function updateAssetUsed(array $newDetails)
    // {
    //     $assetId = $newDetails['assetId'];
    //     $asset = Asset::whereAssetId($assetId);

    //     $userId = $newDetails['user'];

    //     $oldUserId = $newDetails['oldUser'];
    //     $user = $oldAssetUser = User::find($oldUserId);

    //     $newDetails['updated_at'] = Carbon::now();

    //     # kalau sudah dikembalikan
    //     # artinya stocknya jg perlu ditambah
    //     if (isset($newDetails['returned_date'])) {
    //         $running_stock = $user->assetReturned()->where('tbl_asset_used.asset_id', $assetId)->first()->pivot->amount_used;
    //         $asset->asset_running_stock = $asset->asset_running_stock - $running_stock;
    //         $asset->save();

    //     } else {

    //         # kalau end_used dikosongkan
    //         # maka statusnya akan kembali "digunakan"
    //         $asset->asset_running_stock = $asset->asset_running_stock + $details['amount_used'];
    //         $asset->save();
            
    //     }


    //     return $user->asset()->updateExistingPivot($assetId, $details);
    // }

    public function deleteAssetUsed($assetId, $usedId)
    {
        # retrieve amount of assets that being used
        $assetUsed = AssetUsed::find($usedId);
        $amount_used = $assetUsed->amount_used;

        # delete user who using the asset
        $assetUsed->delete();

        # return the stock that being used into the available stock
        $asset = Asset::whereAssetId($assetId);
        $asset->asset_running_stock = $asset->asset_running_stock - $amount_used;
        $asset->save();
    }
}
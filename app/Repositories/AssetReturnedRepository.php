<?php

namespace App\Repositories;

use App\Interfaces\AssetRepositoryInterface;
use App\Interfaces\AssetReturnedRepositoryInterface;
use App\Models\Asset;
use App\Models\AssetReturned;
use DataTables;
use Exception;
use Illuminate\Support\Carbon;

class AssetReturnedRepository implements AssetReturnedRepositoryInterface 
{

    private AssetRepositoryInterface $assetRepository;

    public function __construct(AssetRepositoryInterface $assetRepository)
    {  
        $this->assetRepository = $assetRepository; 
    }

    public function createAssetReturned(array $returnedDetails)
    {
        $asset = $this->assetRepository->getAssetById($returnedDetails['assetId']);
        if ($returnedDetails['amount_returned'] > $asset->asset_running_stock)
            throw new Exception("The amount returned was invalid.");

        AssetReturned::create($returnedDetails);

        $asset->asset_running_stock = $asset->asset_running_stock - $returnedDetails['amount_returned'];
        $asset->save();
    }

    public function deleteAssetReturned($assetId, $returnedId)
    {
        $assetReturned = AssetReturned::find($returnedId);
        $amount_returned = $assetReturned->amount_returned;

        # delete user who using the asset
        $assetReturned->delete();

        # return the stock that being used into the available stock
        $asset = Asset::whereAssetId($assetId);
        $asset->asset_running_stock = $asset->asset_running_stock + $amount_returned;
        $asset->save();
    }
}
<?php

namespace App\Actions\Assets;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\AssetRepositoryInterface;
use App\Models\Asset;

class UpdateAssetAction
{
    use CreateCustomPrimaryKeyTrait;
    private AssetRepositoryInterface $assetRepository;

    public function __construct(AssetRepositoryInterface $assetRepository)
    {
        $this->assetRepository = $assetRepository;
    }

    public function execute(
        String $asset_id,
        Array $new_asset_details
    )
    {
        # Update asset
        $updated_asset = $this->assetRepository->updateAsset($asset_id, $new_asset_details);

        return $updated_asset;
    }
}
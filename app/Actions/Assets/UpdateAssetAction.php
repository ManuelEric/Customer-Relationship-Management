<?php

namespace App\Actions\Assets;

use App\Interfaces\AssetRepositoryInterface;

class UpdateAssetAction
{
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
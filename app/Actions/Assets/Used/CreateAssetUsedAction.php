<?php

namespace App\Actions\Assets\Used;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\AssetRepositoryInterface;
use App\Interfaces\AssetUsedRepositoryInterface;
use App\Models\Asset;
use Illuminate\Http\Request;

class CreateAssetUsedAction
{
    use CreateCustomPrimaryKeyTrait;
    private AssetUsedRepositoryInterface $assetUsedRepository;

    public function __construct(AssetUsedRepositoryInterface $assetUsedRepository)
    {
        $this->assetUsedRepository = $assetUsedRepository;
    }

    public function execute(
        Array $new_asset_used_details
    )
    {

        $new_asset_used = $this->assetUsedRepository->createAssetUsed($new_asset_used_details);

        return $new_asset_used;
    }
}
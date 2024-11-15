<?php

namespace App\Actions\Assets\Returned;

use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\AssetReturnedRepositoryInterface;
use Illuminate\Http\Request;

class CreateAssetReturnedAction
{
    use CreateCustomPrimaryKeyTrait;
    private AssetReturnedRepositoryInterface $assetReturnedRepository;

    public function __construct(AssetReturnedRepositoryInterface $assetReturnedRepository)
    {
        $this->assetReturnedRepository = $assetReturnedRepository;
    }

    public function execute(
        Request $request,
        Array $new_asset_returned_details
    )
    {
        $new_asset_returned_details['asset_used_id'] = $request->used_id;
        unset($new_asset_returned_details['used_id']);

        $new_asset_returned = $this->assetReturnedRepository->createAssetReturned($new_asset_returned_details);

        return $new_asset_returned;
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetUsedRequest;
use App\Interfaces\AssetRepositoryInterface;
use App\Interfaces\AssetUsedRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class AssetUsedController extends Controller
{

    protected AssetRepositoryInterface $assetRepository;
    protected AssetUsedRepositoryInterface $assetUsedRepository;
    protected UserRepositoryInterface $userRepository;

    public function __construct(AssetRepositoryInterface $assetRepository, AssetUsedRepositoryInterface $assetUsedRepository, UserRepositoryInterface $userRepository)
    {
        $this->assetRepository = $assetRepository;
        $this->assetUsedRepository = $assetUsedRepository;
        $this->userRepository = $userRepository;
    }

    public function store(StoreAssetUsedRequest $request)
    {
        $usedDetails = $request->only([
            'assetId',
            'user',
            'amount_used',
            'used_date',
            'condition',
        ]);

        DB::beginTransaction();
        try {

            $this->assetUsedRepository->createAssetUsed($usedDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store asset user failed : ' . $e->getMessage());

        }

        return Redirect::to('master/asset/'.$request->assetId)->withSuccess('Asset user was successfully noted');

    }

    public function show(Request $request)
    {
        $assetId = $request->route('asset');
        $usedId = $request->route('used');

        $asset = $this->assetRepository->getAssetById($assetId);
        $user = $asset->userUsedAsset()->where('tbl_asset_used.id', $usedId)->first();
        
        $employees = $this->userRepository->getAllUsersByRole('employee');
        
        # put view detail asset below
        return view('pages.asset.form')->with(
            [
                'asset' => $asset,
                'employees' => $employees,
                'user' => $user,
                'usedId' => $usedId,
                'request' => $request
            ]
        );
    }

    // public function update(StoreAssetUsedRequest $request)
    // {
    //     $usedDetails = $request->only([
    //         'assetId',
    //         'user',
    //         'oldUser',
    //         'amount_used',
    //         'old_amount_used',
    //         'start_using',
    //         'end_using',
    //         'condition'
    //     ]);

    //     // return $this->assetUsedRepository->updateAssetUsed($usedDetails);

    //     DB::beginTransaction();
    //     try {

    //         $this->assetUsedRepository->updateAssetUsed($usedDetails);
    //         DB::commit();

    //     } catch (Exception $e) {

    //         DB::rollBack();
    //         Log::error('Update asset user failed : ' . $e->getMessage());

    //     }

    //     return Redirect::to('master/asset/'.$request->assetId)->withSuccess('Asset user was successfully updated');
    // }

    public function destroy(Request $request)
    {
        $assetId = $request->route('asset');
        $usedId = $request->route('used');

        DB::beginTransaction();
        try {

            $this->assetUsedRepository->deleteAssetUsed($assetId, $usedId);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete asset used failed : ' . $e->getMessage());

        }

        return Redirect::to('master/asset/'.$request->asset)->withSuccess('Asset used successfully deleted');
    }
}

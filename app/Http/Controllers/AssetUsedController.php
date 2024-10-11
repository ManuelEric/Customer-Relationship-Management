<?php

namespace App\Http\Controllers;

use App\Actions\Assets\Used\CreateAssetUsedAction;
use App\Actions\Assets\Used\DeleteAssetUsedAction;
use App\Http\Requests\StoreAssetUsedRequest;
use App\Interfaces\AssetRepositoryInterface;
use App\Interfaces\AssetUsedRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
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

    public function store(StoreAssetUsedRequest $request, CreateAssetUsedAction $createAssetUsedAction)
    {
        $used_details = $request->safe()->only([
            'assetId',
            'user',
            'amount_used',
            'used_date',
            'condition',
        ]);

        DB::beginTransaction();
        try {

            $createAssetUsedAction->execute($used_details);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store asset user failed : ' . $e->getMessage());
            return Redirect::to('master/asset/' . $request->assetId)->withError('Failed to store user asset');
        }

        return Redirect::to('master/asset/' . $request->assetId)->withSuccess('Asset user was successfully noted');
    }

    public function show(Request $request): JsonResponse
    {
        $asset_id = $request->route('asset');
        $used_id = $request->route('used');

        $asset = $this->assetRepository->getAssetById($asset_id);
        $user = $asset->userUsedAsset()->where('tbl_asset_used.id', $used_id)->first();

        $employees = $this->userRepository->getAllUsersByRole('employee');


        return response()->json([
            'asset' => $asset,
            'employees' => $employees,
            'user' => $user,
            'usedId' => $used_id,
            'amount_returned' => $user->pivot->returned_detail()->sum('amount_returned'),
            'request' => $request
        ]);
    }

    public function destroy(Request $request, DeleteAssetUsedAction $deleteAssetUsedAction)
    {
        $asset_id = $request->route('asset');
        $used_id = $request->route('used');

        DB::beginTransaction();
        try {

            $deleteAssetUsedAction->execute($asset_id, $used_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete asset used failed : ' . $e->getMessage());
            return Redirect::to('master/asset/' . $request->asset)->withError('Failed to delete asset used');
        }

        return Redirect::to('master/asset/' . $request->asset)->withSuccess('Asset used successfully deleted');
    }
}
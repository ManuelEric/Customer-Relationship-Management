<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Interfaces\AssetRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Asset;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class AssetController extends Controller
{
    use CreateCustomPrimaryKeyTrait;

    private AssetRepositoryInterface $assetRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(AssetRepositoryInterface $assetRepository, UserRepositoryInterface $userRepository)
    {
        $this->assetRepository = $assetRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->assetRepository->getAllAssetsDataTables();
        }

        return view('pages.asset.index');
    }

    public function store(StoreAssetRequest $request)
    {
        $assetDetails = $request->only([
            'asset_name',
            'asset_merktype',
            'asset_dateachieved',
            'asset_amount',
            'asset_unit',
            'asset_condition',
            'asset_notes',
        ]);

        $last_id = Asset::max('asset_id');
        $asset_id_without_label = $this->remove_primarykey_label($last_id, 3);
        $asset_id_with_label = 'AS-' . $this->add_digit($asset_id_without_label + 1, 4);

        DB::beginTransaction();
        try {

            $this->assetRepository->createAsset(['asset_id' => $asset_id_with_label] + $assetDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store asset failed : ' . $e->getMessage());
            return Redirect::to('master/asset')->withError('Failed to create asset');

        }

        return Redirect::to('master/asset')->withSuccess('Asset successfully created');
    }

    public function create()
    {
        return view('pages.asset.form');
    }

    public function show(Request $request)
    {
        $assetId = $request->route('asset');

        $asset = $this->assetRepository->getAssetById($assetId);
        
        $employees = $this->userRepository->getAllUsersByRole('employee');
        
        # put view detail asset below
        return view('pages.asset.form')->with(
            [
                'asset' => $asset,
                'employees' => $employees,
                'request' => $request
            ]
        );
    }

    public function edit(Request $request)
    {
        $assetId = $request->route('asset');

        # retrieve asset data by id
        $asset = $this->assetRepository->getAssetById($assetId);
        # put the link to update asset form below
        # example

        return view('pages.asset.form')->with(
            [
                'asset' => $asset,
            ]
        );
    }

    public function update(StoreAssetRequest $request)
    {
        $assetDetails = $request->only([
            'asset_name',
            'asset_merktype',
            'asset_dateachieved',
            'asset_amount',
            'asset_unit',
            'asset_condition',
            'asset_notes',
        ]);

        # retrieve asset id from url
        $assetId = $request->route('asset');

        DB::beginTransaction();
        try {

            $this->assetRepository->updateAsset($assetId, $assetDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update asset failed : ' . $e->getMessage());
            return Redirect::to('master/asset')->withError('Failed to update asset');
        }

        return Redirect::to('master/asset')->withSuccess('Asset successfully updated');;
    }

    public function destroy(Request $request)
    {
        $assetId = $request->route('asset');

        DB::beginTransaction();
        try {

            $this->assetRepository->deleteAsset($assetId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete asset failed : ' . $e->getMessage());
            return Redirect::to('master/asset')->withError('Failed to delete asset');
        }

        return Redirect::to('master/asset')->withSuccess('Asset successfully deleted');
    }
}
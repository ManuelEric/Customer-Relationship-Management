<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\AssetRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Asset;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class AssetController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use LoggingTrait;

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

        return view('pages.master.asset.index');
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
        $asset_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 3) : '0000';
        $asset_id_with_label = 'AS-' . $this->add_digit($asset_id_without_label + 1, 4);
        
        DB::beginTransaction();
        try {

            $assetCreated = $this->assetRepository->createAsset(['asset_id' => $asset_id_with_label] + $assetDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store asset failed : ' . $e->getMessage());
            return Redirect::to('master/asset/' . $asset_id_with_label)->withError('Failed to create asset');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Asset', Auth::user()->first_name . ' '. Auth::user()->last_name, $assetCreated);

        return Redirect::to('master/asset/' . $asset_id_with_label)->withSuccess('Asset successfully created');
    }

    public function create()
    {
        return view('pages.master.asset.form')->with(
            [
                'edit' => true,
            ]
        );
    }

    public function show(Request $request)
    {
        $assetId = $request->route('asset');

        $asset = $this->assetRepository->getAssetById($assetId);

        $employees = $this->userRepository->getAllUsersByRole('employee');

        # put view detail asset below
        return view('pages.master.asset.form')->with(
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

        return view('pages.master.asset.form')->with(
            [
                'edit' => true,
                'asset' => $asset,
            ]
        );
    }

    public function update(StoreAssetRequest $request)
    {
        $assetId = $request->route('asset');
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
        $oldAsset = $this->assetRepository->getAssetById($assetId);

        DB::beginTransaction();
        try {

            $this->assetRepository->updateAsset($assetId, $assetDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update asset failed : ' . $e->getMessage());
            return Redirect::to('master/asset/'.$assetId)->withError('Failed to update asset');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Asset', Auth::user()->first_name . ' '. Auth::user()->last_name, $assetDetails, $oldAsset);

        return Redirect::to('master/asset/'.$assetId)->withSuccess('Asset successfully updated');
    }

    public function destroy(Request $request)
    {
        $assetId = $request->route('asset');
        $asset = $this->assetRepository->getAssetById($assetId);

        DB::beginTransaction();
        try {

            $this->assetRepository->deleteAsset($assetId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete asset failed : ' . $e->getMessage());
            return Redirect::to('master/asset')->withError('Failed to delete asset');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Asset', Auth::user()->first_name . ' '. Auth::user()->last_name, $asset);

        return Redirect::to('master/asset')->withSuccess('Asset successfully deleted');
    }
}

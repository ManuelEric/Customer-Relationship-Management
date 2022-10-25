<?php

namespace App\Http\Controllers;

use App\Interfaces\AssetUsedRepositoryInterface;
use Illuminate\Http\Request;

class AssetUsedController extends Controller
{

    protected AssetUsedRepositoryInterface $assetUsedRepository;

    public function __construct(AssetUsedRepositoryInterface $assetUsedRepository)
    {
        $this->assetUsedRepository = $assetUsedRepository;
    }
    
    public function index(Request $request)
    {
        $assetId = $request->route('asset');
        return $this->assetUsedRepository->getAllAssetUsedDataTables($assetId);
    }
}

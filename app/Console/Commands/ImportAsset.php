<?php

namespace App\Console\Commands;

use App\Interfaces\AssetRepositoryInterface;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportAsset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:asset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import asset data from crm big data v1';

    protected AssetRepositoryInterface $assetRepository;

    public function __construct(AssetRepositoryInterface $assetRepository)
    {
        parent::__construct();
        $this->assetRepository = $assetRepository;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $assets = $this->assetRepository->getAssetFromV1();
        DB::beginTransaction();
        try {

            foreach ($assets as $asset) {
    
                # if asset id from v1 does not exist on v2
                if (!$this->assetRepository->getAssetById($asset->asset_id)) {
    
                    $this->assetRepository->createAsset($asset->toArray());
    
                }
                
            }
            DB::commit();
            Log::info('Import Asset works fine');
            
        } catch (Exception $e) {

            DB::rollBack();
            Log::warning('There\'s something wrong with import asset : '.$e->getMessage());

        }
        return Command::SUCCESS;
    }
}

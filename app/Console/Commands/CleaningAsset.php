<?php

namespace App\Console\Commands;

use App\Interfaces\AssetRepositoryInterface;
use Illuminate\Console\Command;

class CleaningAsset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleaning:asset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleaning asset data from value blank or empty space';

    private AssetRepositoryInterface $assetRepository;

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
        $this->assetRepository->cleaningAsset();
        return Command::SUCCESS;
    }
}

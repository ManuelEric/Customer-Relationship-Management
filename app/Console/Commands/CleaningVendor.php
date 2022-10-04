<?php

namespace App\Console\Commands;

use App\Interfaces\VendorRepositoryInterface;
use App\Models\Vendor;
use Illuminate\Console\Command;

class CleaningVendor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleaning:vendor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleaning vendor data from value blank or empty space';

    private VendorRepositoryInterface $vendorRepository;

    public function __construct(VendorRepositoryInterface $vendorRepository)
    {
        parent::__construct();

        $this->vendorRepository = $vendorRepository;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->vendorRepository->cleaningVendor();
        return Command::SUCCESS;
    }
}

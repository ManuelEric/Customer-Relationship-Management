<?php

namespace App\Console\Commands;

use App\Interfaces\VendorRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportVendor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:vendor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import vendor data from big data v1 into big data v2';

    protected VendorRepositoryInterface $vendorRepository;

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
        $vendor = $this->vendorRepository->getAllVendorFromCRM();
        $vendorDetails = [];
        foreach ($vendor as $vendor) {

            if (!$this->vendorRepository->getVendorById($vendor->vendor_id) && $vendor->vendor_name != "" && $vendor->vendor_name != NULL) {


                $vendorDetails[] = [
                    'vendor_id' => $vendor->vendor_id,
                    'vendor_name' => $vendor->vendor_name,
                    'vendor_address' => $vendor->vendor_address,
                    'vendor_phone' => $vendor->vendor_phone,
                    'vendor_type' => $vendor->vendor_type,
                    'vendor_material' => $vendor->vendor_material,
                    'vendor_size' => $vendor->vendor_size,
                    'vendor_unitprice' => $vendor->vendor_unitprice,
                    'vendor_processingtime' => $vendor->vendor_processingtime,
                    'vendor_notes' => $vendor->vendor_notes,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
        }

        if (count($vendorDetails) > 0) {
            $this->vendorRepository->createVendor($vendorDetails);
        }
        return Command::SUCCESS;
    }
}

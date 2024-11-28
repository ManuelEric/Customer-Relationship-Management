<?php

namespace App\Actions\Vendors;

use App\Interfaces\VendorRepositoryInterface;

class DeleteVendorAction
{
    private VendorRepositoryInterface $vendorRepository;

    public function __construct(VendorRepositoryInterface $vendorRepository)
    {
        $this->vendorRepository = $vendorRepository;
    }

    public function execute(
        $vendor_id
    )
    {
        # delete vendor
        $deleted_vendor = $this->vendorRepository->deleteVendor($vendor_id);

        return $deleted_vendor;
    }
}
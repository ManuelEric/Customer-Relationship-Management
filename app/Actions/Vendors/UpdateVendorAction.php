<?php

namespace App\Actions\Vendors;

use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\VendorRepositoryInterface;

class UpdateVendorAction
{
    use StandardizePhoneNumberTrait;

    private VendorRepositoryInterface $vendorRepository;

    public function __construct(VendorRepositoryInterface $vendorRepository)
    {
        $this->vendorRepository = $vendorRepository;
    }

    public function execute(
        $vendor_id,
        array $new_vendor_details
    ) {

        $new_vendor_details['vendor_phone'] = $this->tnNormalizePhoneNumber($new_vendor_details['vendor_phone']);

        $updated_tag = $this->vendorRepository->updateVendor($vendor_id, $new_vendor_details);

        return $updated_tag;
    }
}

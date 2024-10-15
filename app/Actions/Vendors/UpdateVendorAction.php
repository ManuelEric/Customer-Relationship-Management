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
        Array $new_vendor_details
    )
    {
        unset($new_vendor_details['vendor_phone']);
        $new_vendor_details['vendor_phone'] = $this->tnSetPhoneNumber($new_vendor_details['vendor_phone']);

        $updated_tag = $this->vendorRepository->updateVendor($vendor_id, $new_vendor_details);

        return $updated_tag;
    }
}
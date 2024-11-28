<?php

namespace App\Actions\Vendors;

use App\Http\Requests\StoreVendorRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\VendorRepositoryInterface;
use App\Models\Vendor;

class CreateVendorAction
{
    use CreateCustomPrimaryKeyTrait, StandardizePhoneNumberTrait;
    private VendorRepositoryInterface $vendorRepository;

    public function __construct(VendorRepositoryInterface $vendorRepository)
    {
        $this->vendorRepository = $vendorRepository;
    }

    public function execute(
        StoreVendorRequest $request,
        Array $new_vendor_details
    )
    {

        $new_vendor_details['vendor_phone'] = $this->tnSetPhoneNumber($request->vendor_phone);

        $last_id = Vendor::max('vendor_id');
        $vendor_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 3) : 000;
        $vendor_id_with_label = 'VD-' . $this->add_digit($vendor_id_without_label + 1, 4);

        # store new vendor
        $new_vendor = $this->vendorRepository->createVendor(['vendor_id' => $vendor_id_with_label] + $new_vendor_details);

        return $new_vendor;
    }
}
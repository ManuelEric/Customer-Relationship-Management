<?php

namespace App\Repositories;

use App\Interfaces\VendorRepositoryInterface;
use App\Models\Vendor;

class VendorRepository implements VendorRepositoryInterface 
{
    public function getAllVendor()
    {
        return Vendor::orderBy('vendor_name', 'asc')->get();
    }

    public function getVendorById($vendorId) 
    {
        return Vendor::findOrFail($vendorId);
    }

    public function deleteVendor($vendorId) 
    {
        Vendor::destroy($vendorId);
    }

    public function createVendor(array $vendorDetails) 
    {
        return Vendor::create($vendorDetails);
    }

    public function updateVendor($vendorId, array $newDetails) 
    {
        return Vendor::whereVendorId($vendorId)->update($newDetails);
    }

    public function cleaningVendor()
    {
        Vendor::where('vendor_address', '=', '')->update(
            [
                'vendor_address' => null
            ]
        );

        Vendor::where('vendor_phone', '=', '')->update(
            [
                'vendor_phone' => null
            ]
        );

        Vendor::where('vendor_material', '=', '')->update(
            [
                'vendor_material' => null
            ]
        );

        Vendor::where('vendor_size', '=', '')->update(
            [
                'vendor_size' => null
            ]
        );

        Vendor::where('vendor_processingtime', '=', '')->update(
            [
                'vendor_processingtime' => null
            ]
        );

        Vendor::where('vendor_notes', '=', '')->update(
            [
                'vendor_notes' => null
            ]
        );
    }
}
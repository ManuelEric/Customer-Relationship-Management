<?php

namespace App\Repositories;

use App\Interfaces\VendorRepositoryInterface;
use App\Models\Vendor;
use App\Models\v1\Vendor as V1Vendor;
use DataTables;
use Illuminate\Support\Facades\DB;

class VendorRepository implements VendorRepositoryInterface
{
    public function getAllVendorDataTables()
    {
        return Datatables::eloquent(Vendor::query())->rawColumns(['vendor_address'])->make(true);
    }

    public function getAllVendor()
    {
        return Vendor::orderBy('vendor_name', 'asc')->get();
    }

    public function getVendorById($vendorId)
    {
        // return Vendor::findOrFail($vendorId);
        // return Vendor::whereVendorId($vendorId);
        return Vendor::where('vendor_id', $vendorId)->first();
    }

    public function deleteVendor($vendorId)
    {
        return Vendor::whereVendorId($vendorId)->delete();
    }

    public function createVendor(array $vendorDetails)
    {
        return Vendor::create($vendorDetails);
    }

    public function updateVendor($vendorId, array $newDetails)
    {
        return tap(Vendor::whereVendorId($vendorId))->update($newDetails);
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

    # CRM
    public function getAllVendorFromCRM()
    {
        return V1Vendor::select([
            'vendor_id',
            'vendor_name',
            DB::raw('(CASE
                WHEN vendor_address = "" THEN NULL ELSE vendor_address
            END) as vendor_address'),
            DB::raw('(CASE
                WHEN vendor_phone = "" THEN NULL ELSE vendor_phone
            END) as vendor_phone'),
            'vendor_type',
            DB::raw('(CASE
                WHEN vendor_material = "" THEN NULL ELSE vendor_material
            END) as vendor_material'),
            DB::raw('(CASE
                WHEN vendor_size = "" THEN NULL ELSE vendor_size
            END) as vendor_size'),
            'vendor_unitprice',
            DB::raw('(CASE
                WHEN vendor_processingtime = "" THEN NULL ELSE vendor_processingtime
            END) as vendor_processingtime'),
            DB::raw('(CASE
                WHEN vendor_notes = "" THEN NULL ELSE vendor_notes
            END) as vendor_notes'),
        ])->get();
    }
}

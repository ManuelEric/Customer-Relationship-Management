<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVendorRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\CreateVendorIdTrait;
use App\Interfaces\VendorRepositoryInterface;
use App\Interfaces\VendorTypeRepositoryInterface;
use App\Models\Vendor;
use App\Models\VendorType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class VendorController extends Controller
{
    use CreateCustomPrimaryKeyTrait;

    private VendorRepositoryInterface $vendorRepository;
    private VendorTypeRepositoryInterface $vendorTypeRepository;

    public function __construct(VendorRepositoryInterface $vendorRepository, VendorTypeRepositoryInterface $vendorTypeRepository)
    {
        $this->vendorRepository = $vendorRepository;
        $this->vendorTypeRepository = $vendorTypeRepository;
    }


    public function index()
    {
        return view('vendor.index');
    }

    public function data()
    {
        return $this->vendorRepository->getAllVendorDataTables();
    }

    public function store(StoreVendorRequest $request)
    {
        $vendorDetails = $request->only([
            'vendor_name',
            'vendor_address',
            'vendor_phone',
            'vendor_type',
            'vendor_material',
            'vendor_size',
            'vendor_unitprice',
            'vendor_processingtime',
            'vendor_notes',
        ]);

        $last_id = Vendor::max('vendor_id');
        $vendor_id_without_label = $this->remove_primarykey_label($last_id, 3);
        $vendor_id_with_label = 'VD-' . $this->add_digit($vendor_id_without_label + 1, 4);

        DB::beginTransaction();
        try {

            $this->vendorRepository->createVendor(['vendor_id' => $vendor_id_with_label] + $vendorDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store vendor failed : ' . $e->getMessage());
        }

        return Redirect::to('vendor');
    }

    public function create()
    {
        return view('vendor.form')->with(
            [
                'type' => $this->vendorTypeRepository->getAllVendorType()
            ]
        );
    }

    public function edit(Request $request)
    {
        $vendorId = $request->route('vendor');

        # retrieve vendor type data
        $vendorType = $this->vendorTypeRepository->getAllVendorType();

        # retrieve vendor data by id
        $vendor = $this->vendorRepository->getVendorById($vendorId);
        # put the link to update vendor form below
        # example

        return view('vendor.form')->with(
            [
                'vendor' => $vendor,
                'type' => $vendorType
            ]
        );
    }

    public function update(StoreVendorRequest $request)
    {
        $vendorDetails = $request->only([
            'vendor_name',
            'vendor_address',
            'vendor_phone',
            'vendor_type',
            'vendor_material',
            'vendor_size',
            'vendor_unitprice',
            'vendor_processingtime',
            'vendor_notes',
        ]);

        # retrieve vendor id from url
        $vendorId = $request->route('vendor');

        DB::beginTransaction();
        try {

            $this->vendorRepository->updateVendor($vendorId, $vendorDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update vendor failed : ' . $e->getMessage());
        }

        return Redirect::to('vendor');
    }

    public function destroy(Request $request)
    {
        $vendorId = $request->route('vendor');

        DB::beginTransaction();
        try {

            $this->vendorRepository->deleteVendor($vendorId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete vendor failed : ' . $e->getMessage());
        }

        return Redirect::to('vendor');
    }
}
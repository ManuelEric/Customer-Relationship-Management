<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVendorRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\CreateVendorIdTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\VendorRepositoryInterface;
use App\Interfaces\VendorTypeRepositoryInterface;
use App\Models\Vendor;
use App\Models\VendorType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class VendorController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use StandardizePhoneNumberTrait;
    use LoggingTrait;

    private VendorRepositoryInterface $vendorRepository;
    private VendorTypeRepositoryInterface $vendorTypeRepository;

    public function __construct(VendorRepositoryInterface $vendorRepository, VendorTypeRepositoryInterface $vendorTypeRepository)
    {
        $this->vendorRepository = $vendorRepository;
        $this->vendorTypeRepository = $vendorTypeRepository;
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->vendorRepository->getAllVendorDataTables();
        }

        return view('pages.master.vendor.index');
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
        unset($vendorDetails['vendor_phone']);
        $vendorDetails['vendor_phone'] = $this->setPhoneNumber($request->vendor_phone);

        $last_id = Vendor::max('vendor_id');
        $vendor_id_without_label = $last_id ? $this->remove_primarykey_label($last_id, 3) : 000;
        $vendor_id_with_label = 'VD-' . $this->add_digit($vendor_id_without_label + 1, 4);

        DB::beginTransaction();
        try {

            $newVendor = $this->vendorRepository->createVendor(['vendor_id' => $vendor_id_with_label] + $vendorDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store vendor failed : ' . $e->getMessage());
            return Redirect::to('master/vendor')->withError('Failed to create a new vendor');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Vendor', Auth::user()->first_name . ' '. Auth::user()->last_name, $newVendor);

        return Redirect::to('master/vendor')->withSuccess('Vendor successfully created');
    }

    public function create()
    {
        return view('pages.master.vendor.form')->with(
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

        return view('pages.master.vendor.form')->with(
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
        unset($vendorDetails['vendor_phone']);
        $vendorDetails['vendor_phone'] = $this->setPhoneNumber($request->vendor_phone);

        # retrieve vendor id from url
        $vendorId = $request->route('vendor');

        $oldVendor = $this->vendorRepository->getVendorById($vendorId);

        DB::beginTransaction();
        try {

            $this->vendorRepository->updateVendor($vendorId, $vendorDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update vendor failed : ' . $e->getMessage());
            return Redirect::to('master/vendor')->withError('Failed to update a vendor');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Vendor', Auth::user()->first_name . ' '. Auth::user()->last_name, $vendorDetails, $oldVendor);

        return Redirect::to('master/vendor')->withSuccess('Vendor successfully updated');
    }

    public function destroy(Request $request)
    {
        $vendorId = $request->route('vendor');
        $vendor = $this->vendorRepository->getVendorById($vendorId);

        DB::beginTransaction();
        try {

            $this->vendorRepository->deleteVendor($vendorId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete vendor failed : ' . $e->getMessage());
            return Redirect::to('master/vendor')->withError('Failed to delete a vendor');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Vendor', Auth::user()->first_name . ' '. Auth::user()->last_name, $vendor);

        return Redirect::to('master/vendor')->withSuccess('Vendor successfully deleted');
    }
}

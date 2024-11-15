<?php

namespace App\Http\Controllers;

use App\Actions\Vendors\CreateVendorAction;
use App\Actions\Vendors\DeleteVendorAction;
use App\Actions\Vendors\UpdateVendorAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\CreateVendorIdTrait;
use App\Http\Traits\LoggingTrait;
use App\Http\Traits\StandardizePhoneNumberTrait;
use App\Interfaces\VendorRepositoryInterface;
use App\Interfaces\VendorTypeRepositoryInterface;
use App\Models\Vendor;
use App\Models\VendorType;
use App\Services\Log\LogService;
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

    public function store(StoreVendorRequest $request, CreateVendorAction $createVendorAction, LogService $log_service)
    {
        $new_vendor_details = $request->only([
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
       
        DB::beginTransaction();
        try {

            $new_vendor = $createVendorAction->execute($request, $new_vendor_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_VENDOR, $e->getMessage(), $e->getLine(), $e->getFile(), $new_vendor_details);

            return Redirect::to('master/vendor')->withError('Failed to create a new vendor');
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_VENDOR, 'New vendor has been added', $new_vendor->toArray());

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
        $vendor_id = $request->route('vendor');

        # retrieve vendor type data
        $vendor_type = $this->vendorTypeRepository->getAllVendorType();

        # retrieve vendor data by id
        $vendor = $this->vendorRepository->getVendorById($vendor_id);
        # put the link to update vendor form below
        # example

        return view('pages.master.vendor.form')->with(
            [
                'vendor' => $vendor,
                'type' => $vendor_type
            ]
        );
    }

    public function update(StoreVendorRequest $request, UpdateVendorAction $updateVendorAction, LogService $log_service)
    {
        $new_vendor_details = $request->safe()->only([
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
        $vendor_id = $request->route('vendor');

        DB::beginTransaction();
        try {

            $updated_vendor = $updateVendorAction->execute($vendor_id, $new_vendor_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_VENDOR, $e->getMessage(), $e->getLine(), $e->getFile(), $new_vendor_details);

            return Redirect::to('master/vendor')->withError('Failed to update a vendor');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_VENDOR, 'Vendor has been updated', $updated_vendor->toArray());

        return Redirect::to('master/vendor')->withSuccess('Vendor successfully updated');
    }

    public function destroy(Request $request, DeleteVendorAction $deleteVendorAction, LogService $log_service)
    {
        $vendor_id = $request->route('vendor');
        $vendor = $this->vendorRepository->getVendorById($vendor_id);

        DB::beginTransaction();
        try {

            $deleteVendorAction->execute($vendor_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_VENDOR, $e->getMessage(), $e->getLine(), $e->getFile(), $vendor->toArray());

            return Redirect::to('master/vendor')->withError('Failed to delete a vendor');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_VENDOR, 'Vendor has been deleted', $vendor->toArray());

        return Redirect::to('master/vendor')->withSuccess('Vendor successfully deleted');
    }
}

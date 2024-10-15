<?php

namespace App\Http\Controllers;

use App\Actions\PurchaseRequest\CreatePurchaseRequestAction;
use App\Actions\PurchaseRequest\DeletePurchaseRequestAction;
use App\Actions\PurchaseRequest\PrintPurchaseRequestAction;
use App\Actions\PurchaseRequest\UpdatePurchaseRequestAction;
use App\Enum\LogModule;
use App\Http\Requests\StorePurchaseReqRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\PurchaseDetailRepositoryInterface;
use App\Interfaces\PurchaseRequestRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class PurchaseRequestController extends Controller
{
    use CreateCustomPrimaryKeyTrait;
    use LoggingTrait;

    private PurchaseRequestRepositoryInterface $purchaseRequestRepository;
    private DepartmentRepositoryInterface $departmentRepository;
    private UserRepositoryInterface $userRepository;
    private PurchaseDetailRepositoryInterface $purchaseDetailRepository;

    public function __construct(PurchaseRequestRepositoryInterface $purchaseRequestRepository, DepartmentRepositoryInterface $departmentRepository, UserRepositoryInterface $userRepository, PurchaseDetailRepositoryInterface $purchaseDetailRepository)
    {
        $this->purchaseRequestRepository = $purchaseRequestRepository;
        $this->departmentRepository = $departmentRepository;
        $this->userRepository = $userRepository;
        $this->purchaseDetailRepository = $purchaseDetailRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->purchaseRequestRepository->getAllPurchaseRequestDataTables();
        }

        return view('pages.master.purchase.index');
    }

    public function store(StorePurchaseReqRequest $request, CreatePurchaseRequestAction $createPurchaseRequestAction, LogService $log_service)
    {
        $new_request_details = $request->safe()->only([
            'purchase_department',
            'purchase_statusrequest',
            'purchase_requestdate',
            'purchase_notes',
            'purchase_attachment',
            'requested_by',
        ]);

        DB::beginTransaction();
        try {

            $new_purchase_request = $createPurchaseRequestAction->execute($request, $new_request_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_PURCHASE_REQUEST, $e->getMessage(), $e->getLine(), $e->getFile(), $new_request_details);

            return Redirect::to('master/purchase/create')->withError('Failed to create a purchase request');
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_PURCHASE_REQUEST, 'New purchase request has been added', $new_purchase_request->toArray());

        return Redirect::to('master/purchase/' . $new_purchase_request->purchase_id)->withSuccess('Purchase request successfully created');
    }

    public function create()
    {
        $departments = $this->departmentRepository->getAllDepartment();
        $employees = $this->userRepository->getAllUsersByRole('employee');
        $request_status = ['Urgent', 'Immediately', 'Can Wait', 'Done'];

        return view('pages.master.purchase.form')->with(
            [
                'edit' => true,
                'departments' => $departments,
                'employees' => $employees,
                'requestStatus' => $request_status
            ]
        );
    }

    public function show(Request $request)
    {
        $purchase_id = $request->route('purchase');

        # retrieve purchase data by id
        $purchase_request = $this->purchaseRequestRepository->getPurchaseRequestById($purchase_id);

        $departments = $this->departmentRepository->getAllDepartment();
        $employees = $this->userRepository->getAllUsersByRole('employee');
        $request_status = ['Urgent', 'Immediately', 'Can Wait', 'Done'];


        return view('pages.master.purchase.form')->with(
            [
                'purchaseRequest' => $purchase_request,
                'departments' => $departments,
                'employees' => $employees,
                'requestStatus' => $request_status
            ]
        );
    }

    public function update(StorePurchaseReqRequest $request, UpdatePurchaseRequestAction $updatePurchaseRequestAction, LogService $log_service)
    {
        $new_request_details = $request->only([
            'purchase_department',
            'purchase_statusrequest',
            'purchase_requestdate',
            'purchase_notes',
            'purchase_attachment',
            'requested_by',
        ]);
        $purchase_id = strtoupper($request->route('purchase'));

        DB::beginTransaction();
        try {

            $updated_purchase_request = $updatePurchaseRequestAction->execute($request, $purchase_id, $new_request_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            $log_service->createErrorLog(LogModule::UPDATE_PURCHASE_REQUEST, $e->getMessage(), $e->getLine(), $e->getFile(), $new_request_details);

            return Redirect::to('master/purchase/' . $purchase_id)->withError('Failed to update a purchase request');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_PURCHASE_REQUEST, 'Purchase request has been updated', $updated_purchase_request->toArray());

        return Redirect::to('master/purchase/' . $purchase_id)->withSuccess('Purchase request successfully updated');
    }

    public function edit(Request $request)
    {
        $purchase_id = $request->route('purchase');

        # retrieve purchase data by id
        $purchase_request = $this->purchaseRequestRepository->getPurchaseRequestById($purchase_id);

        $departments = $this->departmentRepository->getAllDepartment();
        $employees = $this->userRepository->getAllUsersByRole('employee');
        $request_status = ['Urgent', 'Immediately', 'Can Wait', 'Done'];

        return view('pages.master.purchase.form')->with(
            [
                'edit' => true,
                'purchaseRequest' => $purchase_request,
                'departments' => $departments,
                'employees' => $employees,
                'requestStatus' => $request_status
            ]
        );
    }

    public function destroy(Request $request, DeletePurchaseRequestAction $deletePurchaseRequestAction, LogService $log_service)
    {
        $purchase_id = $request->route('purchase');

        DB::beginTransaction();
        try {

            $purchase = $this->purchaseRequestRepository->getPurchaseRequestById($purchase_id);
            
            $deletePurchaseRequestAction->execute($purchase_id, $purchase);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            $log_service->createErrorLog(LogModule::DELETE_PURCHASE_REQUEST, $e->getMessage(), $e->getLine(), $e->getFile(), $purchase->toArray());
            return Redirect::to('master/purchase')->withError('Failed to delete purchase request');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_PURCHASE_REQUEST, 'Purchase request has been deleted', $purchase->toArray());

        return Redirect::to('master/purchase')->withSuccess('Purchase Request successfully deleted');
    }

    public function print(Request $request, PrintPurchaseRequestAction $printPurchaseRequestAction, LogService $log_service)
    {
        $purchase_id = $request->route('purchase');
       
        try {
            $pdf = $printPurchaseRequestAction->execute($purchase_id);
        } catch (Exception $e) {
            $log_service->createErrorLog(LogModule::PRINT_PURCHASE_REQUEST, $e->getMessage(), $e->getLine(), $e->getFile(), ['purchase_id' => $purchase_id]);

            return Redirect::to('master/purchase/' . $purchase_id)->withError('Failed to print a purchase request');
        }

        $log_service->createSuccessLog(LogModule::PRINT_PURCHASE_REQUEST, 'Successfully print purchase request', ['purchase_id' => $purchase_id]);

        return $pdf->download($purchase_id . '.pdf');
    }

    public function download($filename)
    {
        # Check if file exists in public/uploaded_file/finance folder
        $file_path = public_path() . '/storage/uploaded_file/finance/' . $filename;

        if (file_exists($file_path)) {

            # Download success
            # create log success
            $this->logSuccess('download', null, 'Purchase Request', Auth::user()->first_name . ' '. Auth::user()->last_name, ['filename' => $filename]);

            # Send Download
            return Response::download($file_path, $filename, [
                'Content-Length: ' . filesize($file_path)
            ]);
        } else {
            # Error
            return Redirect::back()->withError('Requested file does not exist on the server');
        }
    }
}
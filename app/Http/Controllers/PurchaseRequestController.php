<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseReqRequest;
use App\Http\Traits\CreateCustomPrimaryKeyTrait;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\PurchaseDetailRepositoryInterface;
use App\Interfaces\PurchaseRequestRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\PurchaseRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
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

    public function store(StorePurchaseReqRequest $request)
    {

        # create purchase id
        $last_id = PurchaseRequest::max('purchase_id');
        $purchase_id_without_label = $this->remove_primarykey_label($last_id, 4);
        $purchase_id_with_label = 'PCS-' . $this->add_digit((int) $purchase_id_without_label + 1, 4);

        $requestDetails = $request->only([
            'purchase_department',
            'purchase_statusrequest',
            'purchase_requestdate',
            'purchase_notes',
            'purchase_attachment',
            'requested_by',
        ]);
        $requestDetails['purchase_id'] = $purchase_id_with_label;

        DB::beginTransaction();
        try {

            if ($request->hasFile('purchase_attachment')) {

                $file_name = $purchase_id_with_label;
                $file_format = $request->file('purchase_attachment')->getClientOriginalExtension();
                $file_path = $request->file('purchase_attachment')->storeAs('public/uploaded_file/finance', $file_name . '.' . $file_format);
                unset($requestDetails['purchase_attachment']);
                $requestDetails['purchase_attachment'] = $file_name . '.' . $file_format;
            }

            # insert into purchase request
            $newPurchaseRequest = $this->purchaseRequestRepository->createPurchaseRequest($requestDetails);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store purchase request failed : ' . $e->getMessage());
            return Redirect::to('master/purchase/create')->withError('Failed to create a purchase request');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Purchase Request', Auth::user()->first_name . ' '. Auth::user()->last_name, $newPurchaseRequest);

        return Redirect::to('master/purchase/' . $purchase_id_with_label)->withSuccess('Purchase request successfully created');
    }

    public function create()
    {
        $departments = $this->departmentRepository->getAllDepartment();
        $employees = $this->userRepository->getAllUsersByRole('employee');
        $requestStatus = ['Urgent', 'Immediately', 'Can Wait', 'Done'];

        return view('pages.master.purchase.form')->with(
            [
                'edit' => true,
                'departments' => $departments,
                'employees' => $employees,
                'requestStatus' => $requestStatus
            ]
        );
    }

    public function show(Request $request)
    {
        $purchaseId = $request->route('purchase');

        # retrieve purchase data by id
        $purchaseRequest = $this->purchaseRequestRepository->getPurchaseRequestById($purchaseId);

        $departments = $this->departmentRepository->getAllDepartment();
        $employees = $this->userRepository->getAllUsersByRole('employee');
        $requestStatus = ['Urgent', 'Immediately', 'Can Wait', 'Done'];


        return view('pages.master.purchase.form')->with(
            [
                'purchaseRequest' => $purchaseRequest,
                'departments' => $departments,
                'employees' => $employees,
                'requestStatus' => $requestStatus
            ]
        );
    }

    public function update(StorePurchaseReqRequest $request)
    {
        $newDetails = $request->only([
            'purchase_department',
            'purchase_statusrequest',
            'purchase_requestdate',
            'purchase_notes',
            'purchase_attachment',
            'requested_by',
        ]);
        $purchaseId = strtoupper($request->route('purchase'));
        $oldPurchase = $this->purchaseRequestRepository->getPurchaseRequestById($purchaseId);

        DB::beginTransaction();
        try {

            if ($request->hasFile('purchase_attachment')) {

                $file_name = $purchaseId;
                $file_format = $request->file('purchase_attachment')->getClientOriginalExtension();
                $file_path = $request->file('purchase_attachment')->storeAs('public/uploaded_file/finance', $file_name . '.' . $file_format);
                unset($newDetails['purchase_attachment']);
                $newDetails['purchase_attachment'] = $file_name . '.' . $file_format;
            }

            # insert into purchase request
            $this->purchaseRequestRepository->updatePurchaseRequest($purchaseId, $newDetails);

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update purchase request failed : ' . $e->getMessage());
            return Redirect::to('master/purchase/' . $purchaseId)->withError('Failed to update a purchase request');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Purchase Request', Auth::user()->first_name . ' '. Auth::user()->last_name, $newDetails, $oldPurchase);

        return Redirect::to('master/purchase/' . $purchaseId)->withSuccess('Purchase request successfully updated');
    }

    public function edit(Request $request)
    {
        $purchaseId = $request->route('purchase');

        # retrieve purchase data by id
        $purchaseRequest = $this->purchaseRequestRepository->getPurchaseRequestById($purchaseId);

        $departments = $this->departmentRepository->getAllDepartment();
        $employees = $this->userRepository->getAllUsersByRole('employee');
        $requestStatus = ['Urgent', 'Immediately', 'Can Wait', 'Done'];

        return view('pages.master.purchase.form')->with(
            [
                'edit' => true,
                'purchaseRequest' => $purchaseRequest,
                'departments' => $departments,
                'employees' => $employees,
                'requestStatus' => $requestStatus
            ]
        );
    }

    public function destroy(Request $request)
    {
        $purchaseId = $request->route('purchase');

        DB::beginTransaction();
        try {

            $purchase = $this->purchaseRequestRepository->getPurchaseRequestById($purchaseId);
            if ($this->purchaseRequestRepository->deletePurchaseRequest($purchaseId)) {

                # check if file does exist
                if (file_exists(public_path('storage/uploaded_file/finance/' . $purchase->purchase_attachment)))
                    unlink(public_path('storage/uploaded_file/finance/' . $purchase->purchase_attachment));
            }
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete purchase request failed : ' . $e->getMessage());
            return Redirect::to('master/purchase')->withError('Failed to delete purchase request');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Purchase Request', Auth::user()->first_name . ' '. Auth::user()->last_name, $purchase);

        return Redirect::to('master/purchase')->withSuccess('Purchase Request successfully deleted');
    }

    public function print(Request $request)
    {
        $purchaseId = $request->route('purchase');
        $data = [
            'purchase' => $this->purchaseRequestRepository->getPurchaseRequestById($purchaseId),
            'details' => $this->purchaseDetailRepository->getAllPurchaseDetailByPurchaseId($purchaseId)
        ];

        $pdf = Pdf::loadView('pages.master.purchase.print', $data);
        return $pdf->download($purchaseId . '.pdf');
    }

    public function download($filename)
    {
        // Check if file exists in public/uploaded_file/finance folder
        $file_path = public_path() . '/storage/uploaded_file/finance/' . $filename;
        // echo $file_path;exit;
        if (file_exists($file_path)) {

            # Download success
            # create log success
            $this->logSuccess('download', null, 'Purchase Request', Auth::user()->first_name . ' '. Auth::user()->last_name, ['filename' => $filename]);

            // Send Download
            return Response::download($file_path, $filename, [
                'Content-Length: ' . filesize($file_path)
            ]);
        } else {
            // Error
            return Redirect::back()->withError('Requested file does not exist on the server');
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRefundRequest;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\RefundRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class RefundController extends Controller
{
    private RefundRepositoryInterface $refundRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(RefundRepositoryInterface $refundRepository, ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->refundRepository = $refundRepository;
        $this->clientProgramRepository = $clientProgramRepository;
    }

    public function index(Request $request)
    {
        $status =  $request->route('status');
        if ($request->ajax())
            return $this->refundRepository->getAllRefundDataTables($status);

        return view('pages.invoice.refund.index', ['status' => $status]);
    }
    
    public function store(StoreRefundRequest $request)
    {
        $clientprog_id = $request->route('client_program');
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id);
        
        $refundDetails = $request->only([
            'total_payment',
            'total_paid',
            'percentage_refund',
            'refund_amount',
            'tax_percentage',
            'tax_amount',
            'total_refunded'
        ]);

        DB::beginTransaction();
        try {

            # default refund status is 1
            $this->refundRepository->createRefund(['inv_id' => $clientProg->invoice->inv_id, 'status' => 1] + $refundDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store refund failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to create refund');

        }

        return Redirect::to('invoice/client-program/'.$clientprog_id)->withSuccess('Refund successfully created');
        
    }

    public function destroy(Request $request)
    {
        $clientprog_id = $request->route('client_program');
        $clientProg = $this->clientProgramRepository->getClientProgramById($clientprog_id);

        try {

            $this->refundRepository->deleteRefund($clientProg->invoice->inv_id);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Cancel refund failed : ' . $e->getMessage());
            return Redirect::back()->withError('Failed to cancel refund request');

        }

        return Redirect::to('invoice/client-program/'.$clientprog_id)->withSuccess('Refund successfully created');
    }
}

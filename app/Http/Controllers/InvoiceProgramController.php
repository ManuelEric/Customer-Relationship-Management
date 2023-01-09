<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceProgramRequest;
use App\Interfaces\ClientProgramRepositoryInterface;
use App\Interfaces\InvoiceProgramRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class InvoiceProgramController extends Controller
{
    private InvoiceProgramRepositoryInterface $invoiceProgramRepository;
    private ClientProgramRepositoryInterface $clientProgramRepository;

    public function __construct(InvoiceProgramRepositoryInterface $invoiceProgramRepository, ClientProgramRepositoryInterface $clientProgramRepository)
    {
        $this->invoiceProgramRepository = $invoiceProgramRepository;
        $this->clientProgramRepository = $clientProgramRepository;
    }

    public function index(Request $request)
    {
        $status = $request->get('s') !== NULL ? $request->get('s') : null;
        if ($request->ajax())
            return $this->invoiceProgramRepository->getAllInvoiceProgramDataTables($status);

        return view('pages.invoice.client-program.index', ['status' => $status]);
    }

    public function show(Request $request)
    {
        return view('pages.invoice.client-program.form', ['status' => 'view']);
    }

    public function store(StoreInvoiceProgramRequest $request)
    {
        $invoiceDetails = $request->only([
            'currency',
            'curs_rate',
            'session',
            'inv_price_idr',
            'inv_earlybird_idr',
            'inv_discount_idr',
            'inv_totalnumber_idr',
            'inv_words_idr',
            'inv_paymentmethod',
            'invoice_date',
            'inv_duedate',
            'inv_notes',
            'inv_tnc'
        ]);

        DB::beginTransaction();
        try {

            $this->invoiceProgramRepository->createInvoice($invoiceDetails);
            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::error('Store invoice program failed : ' . $e->getMessage());
            return Redirect::to('invoice/client-program/create?prog='.$request->clientProgId)->withError('Failed to add followup plan');

        }

        return Redirect::to('invoice/client-program?s=list')->withSuccess('Invoice has been created');

    }

    public function create(Request $request)
    {
        if (!isset($request->prog) OR !$clientProg = $this->clientProgramRepository->getClientProgramById($request->prog)){
            return Redirect::to('invoice/client-program?s=needed');
        }

        return view('pages.invoice.client-program.form')->with(
            [
                'status' => 'create',
                'clientProg' => $clientProg,
            ]
        );
    }

    public function edit()
    {
        return view('pages.invoice.client-program.form', ['status' => 'edit']);
    }

    public function export()
    {
        return view('pages.invoice.client-program.export.invoice-pdf', ['is_session' => true]);
    }
}

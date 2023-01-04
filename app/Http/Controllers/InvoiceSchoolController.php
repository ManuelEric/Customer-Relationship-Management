<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceSchRequest;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SchoolRepositoryInterface;
use App\Interfaces\SchoolProgramRepositoryInterface;
use App\Interfaces\InvoiceB2bRepositoryInterface;
use App\Http\Traits\CreateInvoiceIdTrait;
use App\Models\Invb2b;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;


class InvoiceSchoolController extends Controller
{
    use CreateInvoiceIdTrait;
    protected SchoolRepositoryInterface $schoolRepository;
    protected SchoolProgramRepositoryInterface $schoolProgramRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected InvoiceB2bRepositoryInterface $invoiceB2bRepository;

    public function __construct(SchoolRepositoryInterface $schoolRepository, SchoolProgramRepositoryInterface $schoolProgramRepository, ProgramRepositoryInterface $programRepository, InvoiceB2bRepositoryInterface $invoiceB2bRepository)
    {
        $this->schoolRepository = $schoolRepository;
        $this->schoolProgramRepository = $schoolProgramRepository;
        $this->programRepository = $programRepository;
        $this->invoiceB2bRepository = $invoiceB2bRepository;
    }

    public function invoice_needed(Request $request)
    {
        if ($request->ajax()) {
            return $this->invoiceB2bRepository->getAllInvoiceNeededSchDataTables();
        }

        $status = 'needed';

        return view('pages.invoice.school-program.index')->with(['status' => $status]);
       
    }

    public function invoice_list(Request $request)
    {
        if ($request->ajax()) {
            return $this->invoiceB2bRepository->getAllInvoiceSchDataTables();
        }

        $status = 'list';

        return view('pages.invoice.school-program.index')->with(['status' => $status]);
       
    }

    public function create(Request $request)
    {
        $schProgId = $request->route('sch_prog');

        $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($schProgId);

        $schoolId = $schoolProgram->sch_id;

        # retrieve school data by id
        $school = $this->schoolRepository->getSchoolById($schoolId);

        return view('pages.invoice.school-program.form')->with(
            [
                'schoolProgram' => $schoolProgram,
                'school' => $school,
                'status' => 'create',
            ]
        );
    }

    public function store(StoreInvoiceSchRequest $request)
    {
        
        $schProgId = $request->route('sch_prog');
        $invoices = $request->all();

        if($invoices['select_currency'] == 'other'){
            $invoices['invb2b_priceidr'] = $invoices['invb2b_priceidr_other']; 
            $invoices['invb2b_discidr'] = $invoices['invb2b_discidr_other']; 
            $invoices['invb2b_participants'] = $invoices['invb2b_participants_other']; 
            $invoices['invb2b_totpriceidr'] = $invoices['invb2b_totpriceidr_other']; 
            $invoices['invb2b_wordsidr'] = $invoices['invb2b_wordsidr_other']; 
        }

        $now = Carbon::now();
        $thisMonth = $now->month;
        
        $last_id = Invb2b::whereMonth('created_at', $thisMonth)->max(DB::raw('substr(invb2b_id, 1, 4)'));
        
        if($last_id == null){
            $last_id = 0;
        }
 
        $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($schProgId);
        $prog_id = $schoolProgram->prog_id;
        
        // Use Trait Create Invoice Id
        $inv_id = $this->getId($last_id, $prog_id);

        $invoices['invb2b_id'] = $inv_id;
        $invoices['schprog_id'] = $schProgId;

        DB::beginTransaction();
        try {

            $this->invoiceB2bRepository->createInvoiceB2b($invoices);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create invoice failed : ' . $e->getMessage());

            return $e->getMessage();
            exit;
            return Redirect::to('invoice/school-program/'. $schProgId . '/create')->withError('Failed to create a new invoice');
        }

        return Redirect::to('invoice/school-program/status/list')->withSuccess('Invoice successfully created');
        
    }

    public function show(Request $request)
    {
        $schProgId = $request->route('sch_prog');
        $invNum = $request->route('invsch');

        $schoolProgram = $this->schoolProgramRepository->getSchoolProgramById($schProgId);

        $schoolId = $schoolProgram->sch_id;

        $school = $this->schoolRepository->getSchoolById($schoolId);

        $invoiceSch = $this->invoiceB2bRepository->getInvoiceB2bById($invNum);

        return view('pages.invoice.school-program.form')->with(
            [
                'schoolProgram' => $schoolProgram,
                'school' => $school,
                'invoiceSch' => $invoiceSch,
                'status' => 'show',
            ]
        );

    }

    public function destroy(Request $request)
    {
        $invNum = $request->route('invsch');
        $schProgId = $request->route('sch_prog');

        DB::beginTransaction();
        try {
           
            $this->invoiceB2bRepository->deleteInvoiceB2b($invNum);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete invoice failed : ' . $e->getMessage());

            return Redirect::to('invoice/school-program/' . $schProgId . '/detail/' . $invNum)->withError('Failed to delete invoice');
        }
 
        return Redirect::to('invoice/school-program/status/list')->withSuccess('Invoice successfully deleted');
    }

}
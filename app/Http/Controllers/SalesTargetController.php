<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesTargetRequest;
use App\Interfaces\SubProgRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SalesTargetRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class SalesTargetController extends Controller
{

    protected SalesTargetRepositoryInterface $salesTargetRepository;
    protected SubProgRepositoryInterface $subProgRepository;
    protected ProgramRepositoryInterface $programRepository;

    public function __construct(SalesTargetRepositoryInterface $salesTargetRepository, ProgramRepositoryInterface $programRepository, subProgRepositoryInterface $subProgRepository)
    {
        $this->salesTargetRepository = $salesTargetRepository;
        $this->programRepository = $programRepository;
        $this->subProgRepository = $subProgRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            return $this->salesTargetRepository->getAllSalesTargetDataTables();
        }

        # retrieve program data
        $programs = $this->programRepository->getAllPrograms();
        $subProg = $this->subProgRepository->getSubProgByMainProgId(1);

        return view('pages.master.sales-target.index')->with(
            [
                'programs' => $programs,
                'subProg' => $subProg,
            ]
        );
    }

    public function create(Request $request){
       
        if ($request->ajax()) {
         
            $id = $request->get('id');
            $program = $this->programRepository->getProgramById($id);
            $sub_prog_id = $program->sub_prog_id;
                return $this->subProgRepository->getSubProgByMainProgId($sub_prog_id);
  
        }
            
        
    }

    public function store(StoreSalesTargetRequest $request)
    {
        $salesTargets = $request->only([
            'prog_id',
            'sub_prog_id',
            'total_participant',
            'total_target',
            'month_year'
        ]);

        $salesTargets['month_year'] .= '-01';

        DB::beginTransaction();
        try {

            $this->salesTargetRepository->createSalesTarget($salesTargets);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create sales target failed : ' . $e->getMessage());

            return Redirect::to('master/sales-target')->withError('Failed to create a new sales target');
        }

        return Redirect::to('master/sales-target')->withSuccess('Sales target successfully created');
    }

    public function update(StoreSalesTargetRequest $request)
    {
        $salesTargets = $request->only([
            'prog_id',
            'sub_prog_id',
            'total_participant',
            'total_target',
            'month_year'
        ]);
        
        $salesTargets['month_year'] .= '-01';

        $salesTargetId = $request->route('sales_target');

        DB::beginTransaction();
        try {

            $this->salesTargetRepository->updateSalesTarget($salesTargetId, $salesTargets);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update sales target failed : ' . $e->getMessage());
            return Redirect::to('master/sales-target')->withError('Failed to update a sales target');
        }

        return Redirect::to('master/sales-target')->withSuccess('Sales target successfully updated');
    }

    public function edit(Request $request)
    {

        if ($request->ajax()) {
            $salesTargetId = $request->route('sales_target');
            $sales_target = $this->salesTargetRepository->getSalesTargetById($salesTargetId);
            
            $date = Carbon::createFromFormat('Y-m-d', $sales_target->month_year);
            $month_year = $date->format('Y-m');
            $sales_target->month_year = $month_year;

            return response()->json($sales_target);
        }
    }

    public function destroy(Request $request)
    {
        $salesTargetId = $request->route('sales_target');

        DB::beginTransaction();
        try {

            $this->salesTargetRepository->deleteSalesTarget($salesTargetId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete sales target failed : ' . $e->getMessage());
            return Redirect::to('master/sales-target')->withError('Failed to delete a sales target');
        }

        return Redirect::to('master/sales-target')->withSuccess('Sales target successfully deleted');
    }

}
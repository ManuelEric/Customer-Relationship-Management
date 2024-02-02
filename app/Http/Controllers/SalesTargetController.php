<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesTargetRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\MainProgRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SalesTargetRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

use function PHPUnit\Framework\returnSelf;

class SalesTargetController extends Controller
{

    use LoggingTrait;
    protected SalesTargetRepositoryInterface $salesTargetRepository;
    protected ProgramRepositoryInterface $programRepository;
    protected MainProgRepositoryInterface $mainProgRepository;

    public function __construct(SalesTargetRepositoryInterface $salesTargetRepository, ProgramRepositoryInterface $programRepository, MainProgRepositoryInterface $mainProgRepository)
    {
        $this->salesTargetRepository = $salesTargetRepository;
        $this->programRepository = $programRepository;
        $this->mainProgRepository = $mainProgRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            return $this->salesTargetRepository->getAllSalesTargetDataTables();
        }

        # retrieve program data
        $programs = $this->programRepository->getAllPrograms();

        $mainPrograms = $this->mainProgRepository->getAllMainProg();


        return view('pages.master.sales-target.index')->with(
            [
                'programs' => $programs,
                'mainPrograms' => $mainPrograms,
            ]
        );
    }


    public function store(StoreSalesTargetRequest $request)
    {
        $salesTargets = $request->only([
            'main_prog_id',
            'prog_id',
            'total_participant',
            'total_target',
            'month_year'
        ]);

        $salesTargets['month_year'] .= '-01';

        DB::beginTransaction();
        try {

            $newSalesTarget = $this->salesTargetRepository->createSalesTarget($salesTargets);
            
            # running command insert target tracking
            Artisan::call('insert:target_tracking_monthly');

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Create sales target failed : ' . $e->getMessage());

            return Redirect::to('master/sales-target')->withError('Failed to create a new sales target');
        }

        # store Success
        # create log success
        $this->logSuccess('store', 'Form Input', 'Sales Target', Auth::user()->first_name . ' '. Auth::user()->last_name, $newSalesTarget);

        return Redirect::to('master/sales-target')->withSuccess('Sales target successfully created');
    }

    public function update(StoreSalesTargetRequest $request)
    {
        $salesTargets = $request->only([
            'main_prog_id',
            'prog_id',
            'total_participant',
            'total_target',
            'month_year'
        ]);
        
        $salesTargets['month_year'] .= '-01';

        $salesTargetId = $request->route('sales_target');
        $oldSalesTarget = $this->salesTargetRepository->getSalesTargetById($salesTargetId);

        DB::beginTransaction();
        try {

            $this->salesTargetRepository->updateSalesTarget($salesTargetId, $salesTargets);
            
            ## Update target tracking
            # running command insert target tracking
            Artisan::call('insert:target_tracking_monthly');
            
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update sales target failed : ' . $e->getMessage());
            return Redirect::to('master/sales-target')->withError('Failed to update a sales target');
        }

        # Update success
        # create log success
        $this->logSuccess('update', 'Form Input', 'Sales Target', Auth::user()->first_name . ' '. Auth::user()->last_name, $salesTargets, $oldSalesTarget);

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
        $salesTarget = $this->salesTargetRepository->getSalesTargetById($salesTargetId);

        DB::beginTransaction();
        try {

            $this->salesTargetRepository->deleteSalesTarget($salesTargetId);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete sales target failed : ' . $e->getMessage());
            return Redirect::to('master/sales-target')->withError('Failed to delete a sales target');
        }

        # Delete success
        # create log success
        $this->logSuccess('delete', null, 'Sales Target', Auth::user()->first_name . ' '. Auth::user()->last_name, $salesTarget);

        return Redirect::to('master/sales-target')->withSuccess('Sales target successfully deleted');
    }

}
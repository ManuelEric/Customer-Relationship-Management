<?php

namespace App\Http\Controllers;

use App\Actions\SalesTargets\CreateSalesTargetAction;
use App\Actions\SalesTargets\DeleteSalesTargetAction;
use App\Actions\SalesTargets\UpdateSalesTargetAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreSalesTargetRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\MainProgRepositoryInterface;
use App\Interfaces\ProgramRepositoryInterface;
use App\Interfaces\SalesTargetRepositoryInterface;
use App\Services\Log\LogService;
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

        $main_programs = $this->mainProgRepository->rnGetAllMainProg();


        return view('pages.master.sales-target.index')->with(
            [
                'programs' => $programs,
                'mainPrograms' => $main_programs,
            ]
        );
    }


    public function store(StoreSalesTargetRequest $request, CreateSalesTargetAction $createSalesTargetAction, LogService $log_service)
    {
        $new_sales_target_details = $request->only([
            'main_prog_id',
            'prog_id',
            'total_participant',
            'total_target',
            'month_year'
        ]);

        DB::beginTransaction();
        try {

            $new_sales_target = $createSalesTargetAction->execute($new_sales_target_details);
            
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();

            $log_service->createErrorLog(LogModule::STORE_SALES_TARGET, $e->getMessage(), $e->getLine(), $e->getFile(), $new_sales_target_details);
            return Redirect::to('master/sales-target')->withError('Failed to create a new sales target');
        }

        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_SALES_TARGET, 'New sales target has been added', $new_sales_target->toArray());

        return Redirect::to('master/sales-target')->withSuccess('Sales target successfully created');
    }

    public function update(StoreSalesTargetRequest $request, UpdateSalesTargetAction $updateSalesTargetAction, LogService $log_service)
    {
        $new_sales_target_details = $request->only([
            'main_prog_id',
            'prog_id',
            'total_participant',
            'total_target',
            'month_year'
        ]);
        
        $sales_target_id = $request->route('sales_target');

        DB::beginTransaction();
        try {

            $updated_sales_target = $updateSalesTargetAction->execute($sales_target_id, $new_sales_target_details);
            
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_SALES_TARGET, $e->getMessage(), $e->getLine(), $e->getFile(), $new_sales_target_details);

            return Redirect::to('master/sales-target')->withError('Failed to update a sales target');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_SALES_TARGET, 'Sales target has been updated', $updated_sales_target->toArray());

        return Redirect::to('master/sales-target')->withSuccess('Sales target successfully updated');
    }

    public function edit(Request $request)
    {

        if ($request->ajax()) {
            $sales_target_id = $request->route('sales_target');
            $sales_target = $this->salesTargetRepository->getSalesTargetById($sales_target_id);
            
            $date = Carbon::createFromFormat('Y-m-d', $sales_target->month_year);
            $month_year = $date->format('Y-m');
            $sales_target->month_year = $month_year;

            return response()->json($sales_target);
        }
    }

    public function destroy(Request $request, DeleteSalesTargetAction $deleteSalesTargetAction, LogService $log_service)
    {
        $sales_target_id = $request->route('sales_target');
        $sales_target = $this->salesTargetRepository->getSalesTargetById($sales_target_id);

        DB::beginTransaction();
        try {

            $deleteSalesTargetAction->execute($sales_target_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_SALES_TARGET, $e->getMessage(), $e->getLine(), $e->getFile(), $sales_target->toArray());
           
            return Redirect::to('master/sales-target')->withError('Failed to delete a sales target');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_SALES_TARGET, 'Sales target has been deleted', $sales_target->toArray());

        return Redirect::to('master/sales-target')->withSuccess('Sales target successfully deleted');
    }

}
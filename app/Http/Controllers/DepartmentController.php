<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Interfaces\DepartmentRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class DepartmentController extends Controller
{
    protected DepartmentRepositoryInterface $departmentRepository;

    public function __construct(DepartmentRepositoryInterface $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->departmentRepository->getAllDepartmentDataTables();
        }

        return view('pages.department.index');
    }

    public function store(StoreDepartmentRequest $request)
    {
        $departmentDetails = $request->only([
            'dept_name',
        ]);

        DB::beginTransaction();
        try {

            $this->departmentRepository->createDepartment($departmentDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Store department failed : ' . $e->getMessage());
        }

        return Redirect::to('master/department')->withSuccess('Department successfully created');
    }

    public function update(StoreDepartmentRequest $request)
    {
        $departmentDetails = $request->only([
            'dept_name',
        ]);

        $id = $request->route('department');

        DB::beginTransaction();
        try {

            $this->departmentRepository->updateDepartment($id, $departmentDetails);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Update department failed : ' . $e->getMessage());
        }

        return Redirect::to('master/department')->withSuccess('Department successfully updated');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('department');

        DB::beginTransaction();
        try {

            $this->departmentRepository->deleteDepartment($id);
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Delete department failed : ' . $e->getMessage());

        }

        return Redirect::to('master/department')->withSuccess('Department successfully deleted');
    }
}

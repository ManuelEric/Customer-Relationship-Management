<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDepartmentAccessRequest;
use App\Http\Requests\UpdateUserAccessRequest;
use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\MenuRepositoryInterface;
use App\Services\Master\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{

    private DepartmentRepositoryInterface $departmentRepository;
    private MenuRepositoryInterface $menuRepository;

    public function __construct(DepartmentRepositoryInterface $departmentRepository, MenuRepositoryInterface $menuRepository)
    {
        $this->departmentRepository = $departmentRepository;
        $this->menuRepository = $menuRepository;
    }

    public function index()
    {        
        $departments = $this->departmentRepository->getAllDepartment();
        $menus = $this->menuRepository->getMenu();

        return view('pages.menus.index')->with(
            [
                'departments' => $departments,
                'menus' => $menus
            ]
        );
    }

    public function fnGetMenuAccess(Request $request, MenuService $menu_service)
    {
        $department_id = $request->route('department');
        $user_id = $request->route('user');
    
        $access_menus = $menu_service->snGetMenuAccess($department_id, $user_id);

        return response()->json($access_menus);
    }

    public function fnUpdateDepartmentAccess(UpdateDepartmentAccessRequest $request, MenuService $menu_service)
    {
        $request_data = $request->only(['department_id', 'menu_id', 'menu_data', 'copy_data', 'export_data', 'param']);
       
        return $menu_service->snUpdateAccess($request_data, 'department');
    }

    public function fnUpdateUserAccess(UpdateUserAccessRequest $request, MenuService $menu_service)
    {
        $request_data = $request->only(['department_id', 'menu_id', 'menu_data', 'copy_data', 'export_data', 'user', 'param']);

        return $menu_service->snUpdateAccess($request_data, 'user');
    }
}

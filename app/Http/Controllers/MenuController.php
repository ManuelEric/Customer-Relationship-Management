<?php

namespace App\Http\Controllers;

use App\Interfaces\DepartmentRepositoryInterface;
use App\Interfaces\MenuRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function getDepartmentAccess(Request $request)
    {
        $departmentId = $request->route('department');
        $userId = $request->route('user');
    
        try {
            
            $data = $copy = $export = $dept_access = $dept_copy = $dept_export = $user_access = $user_copy = $user_export = [];
            $no = 1;
            $html = '';
            $accessMenus = $this->menuRepository->getDepartmentAccess($departmentId);
            if ($accessMenus->count() > 0) {
                foreach ($accessMenus as $menu) {
                    $data[] = $dept_access[] = $menu->id;
                    if ($menu->pivot->copy == true)
                        $copy[] = $dept_copy[] = $menu->id;

                    if ($menu->pivot->export == true)
                        $export[] = $dept_export[] = $menu->id;
                }
            }

            # get the user access
            if ($userId) {
                $userAccessMenus = $this->menuRepository->getUserAccess($userId);
                if ($userAccessMenus->count() > 0) {
                    foreach ($userAccessMenus as $menu) {
                        $data[] = $user_access[] = $menu->id;

                        if ($menu->pivot->copy == true)
                            $copy[] = $user_copy[] = $menu->id;

                        if ($menu->pivot->export == true)
                            $export[] = $user_export[] = $menu->id;
                    }
                }

            }

        } catch (Exception $e) {

            Log::error('Failed to get department access menu : '.$e->getMessage());
            return response()->json(['message' => 'Failed to get department access menu.']);

        }
        
        return response()->json(
            [
                'data' => $data, # selected data menu department
                'copy' => $copy, # selected copy department
                'export' => $export, # selected export department
                'dept_access' => $dept_access, # selected data menu department used for disabling the checkboxes
                'dept_copy' => $dept_copy,
                'dept_export' => $dept_export,
                'user_access' => $user_access, # selected data menu user
                'user_copy' => $user_copy,
                'user_export' => $user_export,
                
            ]
        );
    }

    public function updateDepartmentAccess(Request $request)
    {
        $requestData = $request->only(['department_id', 'menu_id', 'menu_data', 'copy_data', 'export_data', 'param']);
        $departmentId = $requestData['department_id'];
        $param = $requestData['param'];
        $newDetails = [
            'menu_id' => $requestData['menu_id'],
            'copy' => $requestData['copy_data'],
            'export' => $requestData['export_data'],
            'param' => $requestData['param'],
        ];

        DB::beginTransaction();
        try {

            switch ($param) {

                case "menu":
                    $condition = $requestData['menu_data'] === true;
                    break;

                case "copy":
                    $condition = $requestData['copy_data'] === true;
                    break;

                case "export":
                    $condition = $requestData['export_data'] === true;
                    break;
            }

            # if they're check the menu
            if ($condition === true ) { # if selected menu is checked (true)
                $response = $this->menuRepository->createAccessToDepartment($departmentId, $newDetails);
            } 

            else {
                $response = $this->menuRepository->deleteDepartmentAccess($departmentId, $newDetails);
            }

            // if ($requestData['menu_data'] === true) { # if selected menu is checked (true)
            //     $response = $this->menuRepository->createAccessToDepartment($departmentId, $newDetails);
            // } else { # if selected menu is unchecked (false)
            //     $response = $this->menuRepository->deleteDepartmentAccess($departmentId, $newDetails);
            // }

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to update department access menu : '.$e->getMessage());
            return response()->json(['message' => 'Failed to update department access menu.']);

        }

        return response()->json(
            [
                'message' => 'Department access has been updated',
                'data' => $response->pluck('id')->toArray(),
            ]
        );
    }

    public function updateUserAccess(Request $request)
    {
        $requestData = $request->only(['department_id', 'menu_id', 'menu_data', 'copy_data', 'export_data', 'user', 'param']);

        $departmentId = $requestData['department_id'];
        $userId = $requestData['user'];
        $param = $requestData['param'];
        $newDetails = [
            'menu_id' => $requestData['menu_id'],
            'copy' => $requestData['copy_data'],
            'export' => $requestData['export_data'],
            'param' => $param,
        ];

        DB::beginTransaction();
        try {

            switch ($param) {

                case "menu":
                    $condition = $requestData['menu_data'] === true;
                    break;

                case "copy":
                    $condition = $requestData['copy_data'] === true;
                    break;

                case "export":
                    $condition = $requestData['export_data'] === true;
                    break;
            }

            # if they're check the menu
            if ($condition === true ) { # if selected menu is checked (true)
                $response = $this->menuRepository->createAccessToUser($userId, $newDetails);
            } 
            
            # if they're uncheck the menu
            else {
                # check if menu_id is on the department access
                # if the menu_id exists on the department access
                # then delete from department access
                // if ($this->menuRepository->getDepartmentAccessByMenuId($departmentId, $newDetails['menu_id'])) {
                //     $response = $this->menuRepository->deleteDepartmentAccess($departmentId, $newDetails);
                // } 
                
                # but when access is for users
                # then delete from user access
                // else {
                    $response = $this->menuRepository->deleteUserAccess($userId, $newDetails);
                // }

            }

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to update user access menu : '.$e->getMessage());
            return response()->json(['message' => 'Failed to update user access menu.']);

        }

        return response()->json(
            [
                'message' => 'User access has been updated',
                'data' => $response->pluck('id')->toArray(),
            ]
        );
    }
}

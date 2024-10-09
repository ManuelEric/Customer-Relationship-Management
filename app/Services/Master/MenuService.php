<?php

namespace App\Services\Master;

use App\Interfaces\ClientProgramLogMailRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\MenuRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MenuService 
{
    protected ClientRepositoryInterface $clientRepository;
    protected MenuRepositoryInterface $menuRepository;

    public function __construct(MenuRepositoryInterface $menuRepository, ClientRepositoryInterface $clientRepository)
    {
        $this->menuRepository = $menuRepository;
        $this->clientRepository = $clientRepository;
    }

    # Purpose:
    # Get menu access from department access and user access
    public function snGetMenuAccess(int $department_id, string $user_id)
    {
        try {
            
            $menu_department = $copy = $export = $dept_access = $dept_copy = $dept_export = $user_access = $user_copy = $user_export = [];

            $department_access_menus = $this->menuRepository->getDepartmentAccess($department_id);
            
            $access_menus = $this->snMappingMenuAccess($department_access_menus);
            $menu_department = $dept_access = $access_menus['data']; 
            $copy = $dept_copy = $access_menus['copy']; 
            $export = $dept_export = $access_menus['export']; 

            # get the user access
            if ($user_id) {
                $user_access_menus = $this->menuRepository->getUserAccess($user_id);
                
                $access_menus = $this->snMappingMenuAccess($user_access_menus);
                
                $menu_department = array_merge($menu_department, $access_menus['data']);
                $copy = array_merge($copy, $access_menus['copy']); 
                $export = array_merge($export, $access_menus['export']); 
                
                $user_access = $access_menus['data'];
                $user_copy = $access_menus['copy'];
                $user_export = $access_menus['export'];

            }

        } catch (Exception $e) {

            Log::error('Failed to get department access menu : '.$e->getMessage());
            return response()->json(['message' => 'Failed to get department access menu.']);

        }
        
        return 
            [
                'data' => $menu_department, # selected data menu department
                'copy' => $copy, # selected copy department
                'export' => $export, # selected export department
                'dept_access' => $dept_access, # selected data menu department used for disabling the checkboxes
                'dept_copy' => $dept_copy,
                'dept_export' => $dept_export,
                'user_access' => $user_access, # selected data menu user
                'user_copy' => $user_copy,
                'user_export' => $user_export,
                
            ];
    }

    # Local method for getMenuAccess
    private function snMappingMenuAccess(Collection $access_menus)
    {
        $data = $dept_access = $copy = $dept_copy = $export = $dept_export = [];
        if ($access_menus->count() > 0) {
            foreach ($access_menus as $menu) {
                $data[] = $dept_access[] = $menu->id;
                if ($menu->pivot->copy == true)
                    $copy[] = $dept_copy[] = $menu->id;

                if ($menu->pivot->export == true)
                    $export[] = $dept_export[] = $menu->id;
            }
        }

        return ['data' => $data, 'dept_access' => $dept_access, 'copy' => $copy, 'dept_copy' => $dept_copy, 'export' => $export, 'dept_export' => $dept_export];
    }

    # Purpose:
    # update access menu for department or user
    # type is department, user
    public function snUpdateAccess(array $request_data, string $type)
    {
        $department_id = $request_data['department_id'];
        $param = $request_data['param'];
        $new_details = [
            'menu_id' => $request_data['menu_id'],
            'copy' => $request_data['copy_data'],
            'export' => $request_data['export_data'],
            'param' => $request_data['param'],
        ];

        if($type == 'user')
            $user_id = $request_data['user'];

        
        DB::beginTransaction();
        try {

            switch ($param) {

                case "menu":
                    $condition = $request_data['menu_data'] === true;
                    break;

                case "copy":
                    $condition = $request_data['copy_data'] === true;
                    break;

                case "export":
                    $condition = $request_data['export_data'] === true;
                    break;
            }

            # if they're check the menu
            if ($condition === true ) { # if selected menu is checked (true)
                switch ($type) {
                    case 'department':
                        $response = $this->menuRepository->createAccessToDepartment($department_id, $new_details);
                        break;

                    case 'user':
                        $response = $this->menuRepository->createAccessToUser($user_id, $new_details);
                        break;
                    
                    default:
                        Log::error('Failed to update access menu : type is invalid');
                        break;
                }
            } 

            else {
                switch ($type) {
                    case 'department':
                        $response = $this->menuRepository->deleteDepartmentAccess($department_id, $new_details);
                        break;

                    case 'user':
                        $response = $this->menuRepository->deleteUserAccess($user_id, $new_details);
                        break;
                    
                    default:
                        Log::error('Failed to update access menu : type is invalid');
                        break;
                }
            }

            
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error('Failed to update department access menu : '.$e->getMessage());
            return response()->json(['message' => 'Failed to update access menu.']);

        }

        return response()->json(
            [
                'message' => 'Access menu has been updated',
                'data' => $response->pluck('id')->toArray(),
            ]
        );
    }

}
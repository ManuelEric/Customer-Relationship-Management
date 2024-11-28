<?php

namespace App\Repositories;

use App\Interfaces\MenuRepositoryInterface;
use App\Models\Department;
use App\Models\MainMenus;
use Illuminate\Support\Collection;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MenuRepository implements MenuRepositoryInterface
{
    public function getMenu()
    {
        $collection = new Collection();
        $menus = Menu::join('tbl_main_menus', 'tbl_main_menus.id', '=', 'tbl_menus.mainmenu_id')
            ->select([
                'tbl_main_menus.mainmenu_name',
                'tbl_main_menus.order_no as order_no_mainmenu',
                'tbl_menus.id as menu_id',
                'tbl_main_menus.id as main_menu_id',
                'tbl_menus.submenu_name',
                'tbl_menus.submenu_link',
                'tbl_menus.order_no',
                'tbl_main_menus.icon',
            ])->orderBy('tbl_main_menus.order_no', 'asc')->orderBy('tbl_menus.order_no', 'asc')->get();

        foreach ($menus as $menu) {
            $collection->push([
                'order_no' => $menu->order_no_mainmenu,
                'order_no_submenu' => $menu->order_no,
                'menu_id' => $menu->menu_id,
                'mainmenu_id' => $menu->main_menu_id,

                # there is exception for trash mainmenu
                # because mainmenu_name and submenu has different names
                # so in order to make it equal then we need to change trash into recycle
                # otherwise the menu won't selected when it should be selected
                'mainmenu_name' => $menu->mainmenu_name == "Trash" ? "Recycle" : $menu->mainmenu_name,
                'submenu_name' => $menu->submenu_name, 
                'submenu_link' => $menu->submenu_link,
                'copy' => true,
                'export' => true,
                'icon' => $menu->icon,
            ]);
        }

        return $collection;
    }

    public function getUserAccess($userId)
    {
        $user = User::where('id', $userId)->first();
        return $user->access_menus;
    }

    public function getUserAccessById($userId, $menuId)
    {
        $user = User::where('id', $userId)->first();
        return $user->access_menus()->where('tbl_menus.id', $menuId)->first();
    }

    public function getDepartmentAccess($departmentId)
    {
        $department = Department::find($departmentId);
        return $department->access_menus;
    }

    public function getDepartmentAccessByMenuId($departmentId, $menuId)
    {
        $department = Department::find($departmentId);
        return $department->access_menus()->where('tbl_menus.id', $menuId)->first();
    }

    public function createAccessToUser($userId, $newDetails)
    {
        $menu_id = $newDetails['menu_id'];
        $copy = $newDetails['copy'];
        $export = $newDetails['export'];
        $param = $newDetails['param'];
        $user = User::where('id', $userId)->first();

        # check if the menu has on user

        if ($user->access_menus()->where('menu_id', $menu_id)->first() && ($copy === true || $export === true))
            $user->access_menus()->updateExistingPivot($menu_id, ['copy' => $copy, 'export' => $export]);
        // else if ($param == 'copy' || $param == 'export')
        //     $user->access_menus()->attach($menu_id, ['copy' => $copy, 'export' => $export]);
        else
            $user->access_menus()->syncWithoutDetaching([['menu_id' => $menu_id, 'copy' => $copy, 'export' => $export]]);

        return $user->access_menus;
    }

    public function createAccessToDepartment($departmentId, $newDetails)
    {
        $menu_id = $newDetails['menu_id'];
        $copy = $newDetails['copy'];
        $export = $newDetails['export'];
        $param = $newDetails['param'];
        $department = Department::find($departmentId);

        // if ($menu_id && $copy === false && $export === false)
        if ($department->access_menus()->where('menu_id', $menu_id)->first() && $copy === true || $export === true)
            $department->access_menus()->updateExistingPivot($menu_id, ['copy' => $copy, 'export' => $export]);
        else if ($param == 'copy' || $param == 'export') 
            $department->access_menus()->attach($menu_id, ['copy' => $copy, 'export' => $export]);  
        else
            $department->access_menus()->syncWithoutDetaching($menu_id, ['copy' => $copy, 'export' => $export]);

        return $department->access_menus;
    }

    public function deleteUserAccess($userId, $deletedDetails)
    {
        $user = User::where('id', $userId)->first();
        $user->access_menus()->detach($deletedDetails);
        return $user->access_menus;
    }

    public function deleteDepartmentAccess($departmentId, $deletedDetails)
    {
        $department = Department::find($departmentId);
        $department->access_menus()->detach($deletedDetails);
        return $department->access_menus;
    }
}

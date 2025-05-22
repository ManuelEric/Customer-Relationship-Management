<?php

namespace App\Services;

use Illuminate\Support\Collection;

class MenuService
{
    public function getUserAccessibleMenu()
    {
        $user = auth()->user();
            $collection = new Collection();

            # check if the user has authenticated 
            if (isset($user) && (($user->department()->wherePivot('status', 1)->count() > 0 || $user->roles()->where('role_name', 'Super Admin')->count() > 0)) ) {
                if(!Cache::has('menu')){ 
                    foreach ($user->department()->wherePivot('status', 1)->get() as $menus) {
                        foreach ($menus->access_menus as $menu) {
    
                            // $keyMenu = $collection->where('submenu_name', $menu->submenu_name)->keys()->first();
                            // Delete submenu by key
                            // $collection->forget($keyMenu);
                            
                            $collection->push([
                                'order_no' => $menu->mainmenu->order_no,
                                'order_no_submenu' => $menu->order_no,
                                'menu_id' => $menu->pivot->menu_id,
                                'mainmenu_id' => $menu->mainmenu->id,
                                'mainmenu_name' => $menu->mainmenu->mainmenu_name,
                                'submenu_name' => $menu->submenu_name,
                                'submenu_link' => $menu->submenu_link,
                                'copy' => $menu->pivot->copy,
                                'export' => $menu->pivot->export,
                                'icon' => $menu->mainmenu->icon,
                            ]);
                        }
                    }
    
                    if ($user->access_menus->count() > 0) {
    
                        foreach ($user->access_menus as $menu) {
                            // Get key same submenu name 
                            
                            // $keyMenu = $collection->where('submenu_name', $menu->submenu_name)->keys()->first();
                            // Delete submenu by key
                            // $collection->forget($keyMenu);
        
                            $collection->push([
                                'order_no' => $menu->mainmenu->order_no,
                                'order_no_submenu' => $menu->order_no,
                                'menu_id' => $menu->pivot->menu_id,
                                'mainmenu_id' => $menu->mainmenu->id,
                                'mainmenu_name' => $menu->mainmenu->mainmenu_name,
                                'submenu_name' => $menu->submenu_name,
                                'submenu_link' => $menu->submenu_link,
                                'copy' => $menu->pivot->copy,
                                'export' => $menu->pivot->export,
                                'icon' => $menu->mainmenu->icon,
                            ]);
                        }
                    }
    
                    $cacheMenu = Cache::pull('menu', $this->checkRoles($user, $collection));
                    $cacheRoleScopeData = $cacheMenu;
                }else{
                    $cacheRoleScopeData = Cache::get('menu');
                }

                # invoice & receipt PIC
                $invRecPics = [
                    [
                        'name' => env('DIRECTOR_NAME'),
                        'email' => env('DIRECTOR_EMAIL')
                    ],
                    [
                        'name' => env('OWNER_NAME'),
                        'email' => env('OWNER_EMAIL')
                    ]
                ];

                $countAlarm = !Cache::has('countAlarm') ? Cache::put('countAlarm', app('alarm-repository-services')->countAlarm()) : null;
                $notification = !Cache::has('notification') ? Cache::put('notification', app('alarm-repository-services')->notification()) : null;
                $followUp = !Cache::has('followUp') ? Cache::put('followUp', app('follow-up')->getAllFollowupWithin(7)) : null ;
                $birthDay = !Cache::has('birthDay') ? Cache::put('birthDay', app('birthday')->getMenteesBirthdayMonthly(date('Y-m'))) : null ;

                $view->with(
                    $cacheRoleScopeData +
                    [
                        'countAlarm' => $countAlarm ?? Cache::get('countAlarm'),
                        'notification' => $notification ?? Cache::get('notification'),
                        'followUp' => $followUp ?? Cache::get('followUp'),
                        'birthDay' => $birthDay ?? Cache::get('birthDay'),
                        'invRecPics' => $invRecPics,
                        'registrationUrl' => env('REGISTRATION_URL')
                    ]
                );
            }
    }
}
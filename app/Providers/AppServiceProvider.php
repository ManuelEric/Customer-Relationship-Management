<?php

namespace App\Providers;

use App\Interfaces\MenuRepositoryInterface;
use App\Models\Department;
use App\Repositories\MenuRepository;
use App\Models\User;
use App\Repositories\AlarmRepository;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use PDO;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('menu-repository-services', MenuRepository::class);
        $this->app->bind('alarm-repository-services', AlarmRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        if (Auth::check() && Auth::user()->email != 'manuel.eric@all-inedu.com')
            Debugbar::disable();

        view()->composer('*', function ($view) {

            $user = auth()->user();
            $collection = new Collection();


            if (isset($user) && ($user->department()->wherePivot('status', 1)->count() > 0 || $user->roles()->where('role_name', 'admin')->count() > 0) ) {
                foreach ($user->department()->wherePivot('status', 1)->get() as $menus) {
                    foreach ($menus->access_menus as $menu) {
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
                        $keyMenu = $collection->where('submenu_name', $menu->submenu_name)->keys()->first();
                        // Delete submenu by key
                        $collection->forget($keyMenu);
    
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


                # if logged in user is admin
                $department = null;
                if ($user->roles()->where('role_name', 'admin')->exists()) {
                    $isAdmin = true;
                    $department = null;
                    $collection = [];
                    $collection = app('menu-repository-services')->getMenu();
                }

                # if logged in user is from department sales
                if ($user->department()->where('dept_name', 'Client Management')->where('status', 1)->exists()) {
                    $isSales = true;
                    $department = 'Client Management';
                }

                # if logged in user is from department partnership
                if ($user->department()->where('dept_name', 'Business Development')->where('status', 1)->exists()) {
                    $isPartnership = true;
                    $department = 'Business Development';
                }

                # if logged in user is from department finance
                if ($user->department()->where('dept_name', 'Finance & Operation')->where('status', 1)->exists()) {
                    $isFinance = true;
                    $department = 'Finance & Operation';
                }

                # if logged in user is from department digital
                if ($user->department()->where('dept_name', 'Digital')->where('status', 1)->exists()) {
                    $isDigital = true;
                    $department = 'Digital';
                }

                $deptId = $department !== null ? Department::where('dept_name', $department)->first()->id : null;

                $grouped = $collection->sortBy(['order_no', 'order_no_submenu'])->values()->mapToGroups(function (array $item, int $key) {
                    return [
                        $item['mainmenu_name'] => [
                            'order_no_submenu' => $item['order_no_submenu'],
                            'mainmenu_name' => $item['mainmenu_name'],
                            'menu_id' => $item['menu_id'],
                            'submenu_name' => $item['submenu_name'],
                            'submenu_link' => $item['submenu_link'],
                            'copy' => $item['copy'],
                            'export' => $item['export'],
                            'icon' => $item['icon']
                        ]

                    ];
                });

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

                $view->with(
                    [
                        'menus' => $grouped,
                        'isAdmin' => $isAdmin ?? false,
                        'isSales' => $isSales ?? false,
                        'isPartnership' => $isPartnership ?? false,
                        'isFinance' => $isFinance ?? false,
                        'isDigital' => $isDigital ?? false,
                        'loggedIn_user' => $user,
                        'deptId' => $deptId,
                        'countAlarm' => app('alarm-repository-services')->countAlarm(),
                        'notification' => app('alarm-repository-services')->notification(),
                        'invRecPics' => $invRecPics,
                    ]
                );
            }
        });
    }
}

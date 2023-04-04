<?php

namespace App\Providers;

use App\Interfaces\MenuRepositoryInterface;
use App\Repositories\MenuRepository;
use App\Models\User;
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
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view) {

            $user = auth()->user();
            $collection = new Collection();

            if (isset($user) && ($user->department->count() > 0 || $user->roles()->where('role_name', 'admin')->count() > 0) ) {
                foreach ($user->department as $menus) {
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

                # if logged in user is admin
                if ($user->roles()->where('role_name', 'admin')->exists()) {
                    $isAdmin = true;
                    $collection = [];
                    $collection = app('menu-repository-services')->getMenu();
                }

                # if logged in user is from department sales
                if ($user->department()->where('dept_name', 'Client Management')->exists()) {
                    $isSales = true;
                }

                # if logged in user is from department partnership
                if ($user->department()->where('dept_name', 'Business Development')->exists()) {
                    $isPartnership = true;
                }

                # if logged in user is from department finance
                if ($user->department()->where('dept_name', 'Finance & Operation')->exists()) {
                    $isFinance = true;
                }

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
                            'icon' => $item['icon'],
                        ]

                    ];
                });

                $view->with(
                    [
                        'menus' => $grouped,
                        'isAdmin' => $isAdmin ?? false,
                        'isSales' => $isSales ?? false,
                        'isPartnership' => $isPartnership ?? false,
                        'isFinance' => $isFinance ?? false,
                    ]
                );
            }
        });
    }
}

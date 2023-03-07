<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
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
        //
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

            if (isset($user) && $user->department->count() > 0) {
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

                $view->with('menus', $grouped);
            }
        });
    }
}

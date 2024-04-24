<?php

namespace App\Providers;

use App\Interfaces\MenuRepositoryInterface;
use App\Models\Department;
use App\Repositories\MenuRepository;
use App\Models\User;
use App\Repositories\AlarmRepository;
use App\Repositories\ClientRepository;
use App\Repositories\FollowupRepository;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
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
        $this->app->bind('follow-up', FollowupRepository::class);
        $this->app->bind('birthday', ClientRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Queue::after(function (JobProcessed $event) {
            Log::debug('Queue : '.json_encode($event).' has ran');
        });

        view()->composer('*', function ($view) {

            $user = auth()->user();
            $collection = new Collection();

            # check if the user has authenticated 
            if (isset($user) && (($user->department()->wherePivot('status', 1)->count() > 0 || $user->roles()->where('role_name', 'Super Admin')->count() > 0)) ) {
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

                $roleScopeData = $this->checkRoles($user, $collection);                

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
                    $roleScopeData +
                    [
                        'countAlarm' => app('alarm-repository-services')->countAlarm(),
                        'notification' => app('alarm-repository-services')->notification(),
                        'followUp' => app('follow-up')->getAllFollowupWithin(7),
                        'birthDay' => app('birthday')->getMenteesBirthdayMonthly(date('Y-m')),
                        'invRecPics' => $invRecPics,
                        'registrationUrl' => env('REGISTRATION_URL')
                    ]
                );
            }
        });
    }



    
    private function checkRoles($user, $collection)
    {

        # Session user_role used for query new leads and raw data

        # if logged in user is admin
        if ($user->roles()->where('role_name', 'Super Admin')->count() > 0) {
            $collection = [];
            $collection = app('menu-repository-services')->getMenu();
            $isSuperAdmin = true;
        }
        

        # get department ID
        # its used to insert department_id when creating lead source
        // $deptId = $department !== null ? Department::where('dept_name', $department)->first()->id : null;
        $deptId = $user->department()->first()->id ?? null;

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

        # default variables
        $response = [
            'isDigital' => false,
            'isSales' => false,
            'isSalesAdmin' => false,
            'isPartnership' => false,
            'isFinance' => false,
            'isSuperAdmin' => $isSuperAdmin ?? false,
            'menus' => $grouped,
            'loggedIn_user' => $user,
            'deptId' => $deptId
        ];

        $entries = $this->checkUserDepartment($user);
        $index = 0;
        while ($index < count($entries)) {

            $response["{$entries[$index]['alias']}"] = $entries[$index]['status'];
            $index++;

        }

        return $response;
    }



    private function checkUserDepartment($user)
    {
        # initiate default variables
        $entries = [
            [
                'department' => 'Client Management',
                'alias' => 'isSales',
                'status' => false,
            ],
            [
                'department' => 'Business Development',
                'alias' => 'isPartnership',
                'status' => false,
            ],
            [
                'department' => 'Finance & Operation',
                'alias' => 'isFinance',
                'status' => false,
            ],
            [
                'department' => 'Digital',
                'alias' => 'isDigital',
                'status' => false,
            ]
        ];

        $index = 0;
        while ($index < count($entries)) 
        {

            # if user logged in user is from the department
            if ($user->department()->where('dept_name', $entries[$index]['department'])->wherePivot('status', 1)->count() > 0) {
                
                $entries[$index]['status'] = true;

                if ($user->roles()->where('role_name', 'Admin')->count() > 0){
                    $entries[$index]['alias'] = 'isSalesAdmin';
                }

            }
            $index++;
        }

        return $entries;
    }
}

<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';
    // public const ADMIN = '/dashboard';
    public const ADMIN = '/dashboard2';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::group(['middleware' => ['web', 'auth', 'auth.department', 'auth.expires']], function() {

                Route::middleware('web')
                    ->prefix('master')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/master.php'));
    
                Route::middleware('web')
                    ->prefix('client')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/client.php'));
    
                Route::middleware('web')
                    ->prefix('user')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/user.php'));
    
                Route::middleware('web')
                    ->prefix('instance')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/instance.php'));
    
                Route::middleware('web')
                    ->prefix('program')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/program.php'));
    
                Route::middleware('web')
                    ->prefix('invoice')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/invoice.php'));
    
                Route::middleware('web')
                    ->prefix('receipt')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/receipt.php'));
    
                Route::middleware('web')
                    ->prefix('report')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/report.php'));

                Route::middleware('web')
                    ->prefix('recycle')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/recycle.php'));

                Route::middleware('web')
                    ->prefix('restore')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/restore.php'));

                Route::middleware('web')
                    ->prefix('request-sign')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/request-sign.php'));
    
                Route::middleware('web')
                    ->prefix('menus')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/menus.php'));
            });

        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
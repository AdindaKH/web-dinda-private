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
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Redirect user based on their role
     */
    // public static function redirectToByRole($user)
    // {
    //     return match ($user->role) {
    //         'pimpinan' => '/dashboard-pimpinan',
    //         'operator' => '/dashboard-operator',
    //         default => '/dashboard',
    //     };
    // }
    
    public static function redirectToByRole($user)
    {
        if ($user->role === 'pimpinan') {
            return '/pimpinan/dashboard';
        } elseif ($user->role === 'operator') {
            return '/operator/dashboard';
        }

        // Default redirect jika role tidak dikenal
        return '/';
    }

}

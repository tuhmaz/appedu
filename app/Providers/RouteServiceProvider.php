<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Http\RateLimiter\ApiRateLimiter;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // تهيئة Rate Limiting
        $rateLimiter = new ApiRateLimiter();
        $rateLimiter->configureRateLimiting();

        $this->routes(function () {
            // Public API Routes (No Authentication)
            Route::middleware(['api_rate_limit:public_api'])
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware(['web'])
                ->group(base_path('routes/web.php'));
        });
    }
}

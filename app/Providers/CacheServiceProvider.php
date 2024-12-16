<?php

namespace App\Providers;

use App\Services\CacheService;
use Illuminate\Support\ServiceProvider;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * تسجيل الخدمات
     */
    public function register(): void
    {
        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService();
        });
    }

    /**
     * تشغيل الخدمات
     */
    public function boot(): void
    {
        //
    }
}

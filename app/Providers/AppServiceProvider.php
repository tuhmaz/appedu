<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use App\Http\Middleware\SecurityHeadersMiddleware;
use App\Http\Middleware\PerformanceMonitorMiddleware;
use App\Models\Article;
use App\Observers\ArticleObserver;
use App\Models\News;
use App\Observers\NewsObserver;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load Passport keys
        Passport::loadKeysFrom(__DIR__.'/../secrets/oauth');

        // Custom Vite styles
        Vite::useStyleTagAttributes(function (?string $src, string $url, ?array $chunk, ?array $manifest) {
            if ($src !== null) {
                return [
                    'class' => preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?core)-?.*/i", $src) ? 'template-customizer-core-css' :
                              (preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?theme)-?.*/i", $src) ? 'template-customizer-theme-css' : '')
                ];
            }
            return [];
        });

        // Register the observer for Article model
        Article::observe(ArticleObserver::class);

        // Register the observer for News model
        News::observe(NewsObserver::class);

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);

        // إضافة ماكرو للكاش التلقائي للاستعلامات
        Builder::macro('cacheFor', function ($minutes = 60) {
            $key = 'query_' . md5(json_encode([
                $this->toSql(),
                $this->getBindings(),
                auth()->id()
            ]));

            return Cache::remember($key, now()->addMinutes($minutes), function () {
                return $this->get();
            });
        });

        // تحسين أداء قاعدة البيانات
        DB::listen(function ($query) {
            if ($query->time > 100) { // تسجيل الاستعلامات التي تستغرق أكثر من 100 مللي ثانية
                Log::warning('Slow Query: ' . $query->sql, [
                    'time' => $query->time,
                    'bindings' => $query->bindings
                ]);
            }
        });

        // إضافة ضغط للمحتوى
        Response::macro('cachedView', function ($view, $data = [], $minutes = 60) {
            $key = 'view_' . md5($view . serialize($data) . auth()->id());
            
            return Cache::remember($key, now()->addMinutes($minutes), function () use ($view, $data) {
                return response(view($view, $data))
                    ->header('Content-Encoding', 'gzip')
                    ->setContent(gzencode(view($view, $data)->render(), 9));
            });
        });

        // تطبيق middleware الأمان على جميع الطلبات
        $this->app['router']->pushMiddlewareToGroup('web', SecurityHeadersMiddleware::class);
        
        // تطبيق middleware مراقبة الأداء
        $this->app['router']->pushMiddlewareToGroup('web', PerformanceMonitorMiddleware::class);

        view()->share('livewireLoaded', false);
    }
}

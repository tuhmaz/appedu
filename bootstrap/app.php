<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\UpdateUserLastActivity;
use App\Http\Middleware\SwitchDatabase;
use App\Http\Middleware\SetDatabaseMiddleware;
use App\Http\Middleware\TrackFailedLogins;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // الـ Middleware الأساسية
        $middleware->web(EncryptCookies::class);
        $middleware->web(AddQueuedCookiesToResponse::class);
        $middleware->web(StartSession::class);
        $middleware->web(ShareErrorsFromSession::class);
        $middleware->web(VerifyCsrfToken::class);
        $middleware->web(SubstituteBindings::class);

        // Middleware التطبيق
        $middleware->web(LocaleMiddleware::class);
        $middleware->web(UpdateUserLastActivity::class);
        $middleware->web(SwitchDatabase::class);
        $middleware->web(TrackFailedLogins::class);
        $middleware->web(\App\Http\Middleware\TrackVisitor::class);

        // تسجيل middleware aliases
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })->create();

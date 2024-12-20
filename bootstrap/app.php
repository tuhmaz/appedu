<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\UpdateUserLastActivity;
use App\Http\Middleware\SwitchDatabase;
use App\Http\Middleware\SetDatabaseMiddleware;
use App\Http\Middleware\TrackFailedLogins;
use App\Http\Middleware\ValidateDatabaseMiddleware;
use App\Http\Middleware\ApiKeyMiddleware;
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
        $middleware->web([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            LocaleMiddleware::class,
            UpdateUserLastActivity::class,
            SwitchDatabase::class,
            TrackFailedLogins::class,
            \App\Http\Middleware\TrackVisitor::class,
        ]);

        $middleware->api([
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            SubstituteBindings::class,
            ApiKeyMiddleware::class,
            ValidateDatabaseMiddleware::class,
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'check.mobile.api' => \App\Http\Middleware\CheckMobileApiKey::class,
            'validate.database' => ValidateDatabaseMiddleware::class,
            'api.key' => ApiKeyMiddleware::class,
        ]);

        $middleware->group('api', [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ValidateDatabaseMiddleware::class,
            ApiKeyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

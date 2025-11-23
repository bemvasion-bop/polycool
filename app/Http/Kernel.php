<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     */
    protected $middleware = [
        // Handle trusted proxies
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Http\Middleware\TrustProxies::class,

        // Prevent requests during maintenance
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,

        // Validate the content length
        \Illuminate\Http\Middleware\ValidatePostSize::class,

        // Trim strings from input
        \Illuminate\Foundation\Http\Middleware\TrimStrings::class,

        // Convert empty strings to null
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // API throttle
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     */
    protected $routeMiddleware = [
        // AUTH
        'auth' => \App\Http\Middleware\Authenticate::class,

        // AUTH BASIC
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,

        // CACHE HEADERS
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,

        // CAN (authorization)
        'can' => \Illuminate\Auth\Middleware\Authorize::class,

        // GUEST REDIRECT
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,

        // PASSWORD CONFIRMATION
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,

        // SIGNED URLS
        'signed' => \App\Http\Middleware\ValidateSignature::class,

        // THROTTLING
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

        // CSRF
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // ⭐ OUR CUSTOM ROLE MIDDLEWARE
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'role' => \App\Http\Middleware\CheckRole::class,
        ,

    ];
}

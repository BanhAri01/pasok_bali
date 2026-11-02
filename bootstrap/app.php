<?php

use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureBusinessMember;
use App\Http\Middleware\SecureHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecureHeaders::class);
        $middleware->trustHosts(at: fn () => array_filter([parse_url((string) config('app.url'), PHP_URL_HOST)]), subdomains: false);
        $middleware->validateCsrfTokens(except: ['webhook/*']);
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn (Request $request) => $request->user()?->isAdmin() ? route('admin.index') : route('dashboard'));
        $middleware->alias([
            'usaha' => EnsureBusinessMember::class,
            'admin' => EnsureAdmin::class,
        ]);
        $middleware->prependToPriorityList(before: SubstituteBindings::class, prepend: EnsureBusinessMember::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

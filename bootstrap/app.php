<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'admin/login',
            'fabricant/login',
            'admin/signalements/*',
            'fabricant/*',
            'verify/signaler',
            'verify-code',
        ]);
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin/*')) {
                return route('admin.login');
            }
            // Les routes publiques de vérification ne redirigent pas vers login
            if ($request->is('verify*') || $request->is('verify-code')) {
                return null;
            }
            return route('fabricant.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
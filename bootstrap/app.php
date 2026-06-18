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
        $middleware->redirectGuestsTo('/login');

        $middleware->validateCsrfTokens(except: [
            '/buy',
            '/rating',
            '/login',
            '/chat/send',
            '/sell/create',
            '/sell/delete',
            '/sell/status',
            '/logout',
            '/profile/update',
            '/profile/password',
            '/profile/delete',
            '/forgot-password/send-otp',
            '/forgot-password/verify-otp',
            '/forgot-password/reset',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

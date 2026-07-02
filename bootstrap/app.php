<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*', headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB);

        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('admin', 'admin/*')) {
                return route('admin.auth.login');
            }
            if ($request->is('reseller', 'reseller/*')) {
                return route('reseller.login');
            }
            return route('member.login');
        });

        $middleware->alias([
            'admin'    => \App\Http\Middleware\IsAdmin::class,
            'member'   => \App\Http\Middleware\IsMember::class,
            'reseller' => \App\Http\Middleware\IsReseller::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            '/ad/impression',
            '/ad/click',
            '/stripe/webhook',
            '/whmcs/webhook',
            '/membership/ssl-success',
            '/membership/ssl-fail',
            '/membership/ssl-cancel',
            '/membership/ssl-ipn',
        ]);
    })
    ->withExceptions(function (Exceptions $_): void {})->create();

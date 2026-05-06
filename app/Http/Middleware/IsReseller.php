<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsReseller
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->type == config('settings.user_type.reseller')) {
            return $next($request);
        }

        return redirect()->route('reseller.login');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->type == config('settings.user_type.customer')) {
            return $next($request);
        }

        return redirect()->route('customer.login');
    }
}

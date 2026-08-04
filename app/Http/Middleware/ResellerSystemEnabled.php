<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Blocks every reseller entry point — portal, REST API and admin management —
 * while the reseller system is switched off in admin settings.
 */
class ResellerSystemEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (reseller_system_enabled()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => __tr('The reseller system is currently disabled.'),
            ], 404);
        }

        abort(404);
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $start    = microtime(true);
        $response = $next($request);
        $duration = (int) ((microtime(true) - $start) * 1000);

        try {
            ApiLog::create([
                'user_id'          => $request->user()?->id,
                'endpoint'         => $request->path(),
                'method'           => $request->method(),
                'request_payload'  => $request->except(['password', 'password_confirmation', 'token']),
                'response_payload' => json_decode($response->getContent(), true),
                'status_code'      => $response->getStatusCode(),
                'ip_address'       => $request->ip(),
                'duration_ms'      => $duration,
            ]);
        } catch (\Exception $e) {
            // fail silently — logging must not break the API response
        }

        return $response;
    }
}

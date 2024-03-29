<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $ip = $request->ip();
        $host = $request->getHost();

        $key = "rate_limit:{$ip}:{$host}";
        $maxAttempts = 1;
        $decaySeconds = 60;

        if (Cache::has($key)) {
            return response()->json(['message' => 'Too many requests.'], 429);
        }

        Cache::put($key, true, $decaySeconds);

        return $next($request);
    }
}

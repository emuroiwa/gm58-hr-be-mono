<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitApi
{
    /**
     * Handle an incoming request.
     * Implements rate limiting for API requests.
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $key = $this->resolveRequestSignature($request);
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($key);
            
            return response()->json([
                'message' => 'Too Many Attempts',
                'retry_after' => $retryAfter,
                'rate_limit' => [
                    'limit' => $maxAttempts,
                    'remaining' => 0,
                    'reset' => now()->addSeconds($retryAfter)->timestamp,
                ]
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers
        $remaining = $maxAttempts - RateLimiter::attempts($key);
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $remaining));
        $response->headers->set('X-RateLimit-Reset', now()->addMinutes($decayMinutes)->timestamp);

        return $response;
    }

    /**
     * Resolve request signature for rate limiting
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $user = $request->user();
        
        if ($user) {
            // Rate limit by user
            return 'api_rate_limit:user:' . $user->id;
        }

        // Rate limit by IP for unauthenticated requests
        return 'api_rate_limit:ip:' . $request->ip();
    }
}

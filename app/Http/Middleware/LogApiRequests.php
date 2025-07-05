<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogApiRequests
{
    /**
     * Handle an incoming request.
     * Logs API requests for monitoring and debugging.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);
        
        // Log incoming request
        $this->logRequest($request);
        
        $response = $next($request);
        
        // Log response
        $this->logResponse($request, $response, $start);
        
        return $response;
    }

    /**
     * Log incoming request details
     */
    private function logRequest(Request $request)
    {
        $user = $request->user();
        
        $logData = [
            'type' => 'api_request',
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $user?->id,
            'company_id' => $user?->company_id,
            'timestamp' => now()->toISOString(),
        ];

        // Log request body for POST/PUT/PATCH (excluding sensitive data)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $body = $request->all();
            
            // Remove sensitive fields
            $sensitiveFields = ['password', 'password_confirmation', 'token', 'secret'];
            foreach ($sensitiveFields as $field) {
                if (isset($body[$field])) {
                    $body[$field] = '[REDACTED]';
                }
            }
            
            $logData['request_body'] = $body;
        }

        Log::channel('api')->info('API Request', $logData);
    }

    /**
     * Log response details
     */
    private function logResponse(Request $request, Response $response, float $start)
    {
        $duration = round((microtime(true) - $start) * 1000, 2); // Duration in milliseconds
        $user = $request->user();
        
        $logData = [
            'type' => 'api_response',
            'method' => $request->method(),
            'path' => $request->path(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'user_id' => $user?->id,
            'company_id' => $user?->company_id,
            'timestamp' => now()->toISOString(),
        ];

        // Log response body for errors
        if ($response->getStatusCode() >= 400) {
            $content = $response->getContent();
            if ($content && is_string($content)) {
                $logData['response_body'] = json_decode($content, true) ?? $content;
            }
        }

        // Determine log level based on status code
        $logLevel = 'info';
        if ($response->getStatusCode() >= 500) {
            $logLevel = 'error';
        } elseif ($response->getStatusCode() >= 400) {
            $logLevel = 'warning';
        }

        Log::channel('api')->{$logLevel}('API Response', $logData);
    }
}

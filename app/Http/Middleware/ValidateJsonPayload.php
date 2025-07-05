<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateJsonPayload
{
    /**
     * Handle an incoming request.
     * Validates JSON payload for API requests.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only validate for requests that should have JSON body
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH']) && 
            $request->is('api/*')) {
            
            $contentType = $request->header('Content-Type');
            
            // Check if content type is JSON
            if ($contentType && str_contains($contentType, 'application/json')) {
                $content = $request->getContent();
                
                if (!empty($content)) {
                    json_decode($content);
                    
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        return response()->json([
                            'message' => 'Invalid JSON payload',
                            'error' => json_last_error_msg()
                        ], 400);
                    }
                }
            }
        }

        return $next($request);
    }
}

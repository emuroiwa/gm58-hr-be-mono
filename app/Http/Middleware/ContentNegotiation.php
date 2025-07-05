<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentNegotiation
{
    /**
     * Handle an incoming request.
     * Handles content negotiation for API responses.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure JSON content type for API requests
        if ($request->is('api/*')) {
            $request->headers->set('Accept', 'application/json');
            
            // Set content type for requests with body
            if (in_array($request->method(), ['POST', 'PUT', 'PATCH']) && 
                !$request->headers->has('Content-Type')) {
                $request->headers->set('Content-Type', 'application/json');
            }
        }

        $response = $next($request);

        // Ensure JSON response for API routes
        if ($request->is('api/*') && $response instanceof \Illuminate\Http\JsonResponse) {
            $response->headers->set('Content-Type', 'application/json');
        }

        return $response;
    }
}

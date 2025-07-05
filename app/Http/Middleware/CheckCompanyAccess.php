<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyAccess
{
    /**
     * Handle an incoming request.
     * Ensures users can only access data from their own company.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$user->company_id) {
            return response()->json(['message' => 'User not associated with any company'], 403);
        }

        // Check if company is active
        if (!$user->company || !$user->company->is_active) {
            return response()->json(['message' => 'Company account is inactive'], 403);
        }

        // Add company_id to request for easy access in controllers
        $request->merge(['company_id' => $user->company_id]);
        
        // Store company_id in request attributes for use in other middleware/controllers
        $request->attributes->set('company_id', $user->company_id);

        return $next($request);
    }
}

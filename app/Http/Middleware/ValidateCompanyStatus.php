<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateCompanyStatus
{
    /**
     * Handle an incoming request.
     * Validates company subscription and status.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user || !$user->company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $company = $user->company;

        // Check if company is active
        if (!$company->is_active) {
            return response()->json([
                'message' => 'Company account is suspended',
                'code' => 'COMPANY_SUSPENDED'
            ], 403);
        }

        // Check subscription status (if you have subscription management)
        if (isset($company->subscription_status) && $company->subscription_status === 'expired') {
            return response()->json([
                'message' => 'Company subscription has expired',
                'code' => 'SUBSCRIPTION_EXPIRED'
            ], 402); // Payment Required
        }

        // Check employee limit (if you have plans with limits)
        if (isset($company->employee_limit)) {
            $employeeCount = $company->employees()->where('status', 'active')->count();
            if ($employeeCount >= $company->employee_limit) {
                // Allow read operations but block create operations
                if (in_array($request->method(), ['POST']) && 
                    str_contains($request->path(), 'employees')) {
                    return response()->json([
                        'message' => 'Employee limit reached for your plan',
                        'current_count' => $employeeCount,
                        'limit' => $company->employee_limit,
                        'code' => 'EMPLOYEE_LIMIT_REACHED'
                    ], 402);
                }
            }
        }

        return $next($request);
    }
}

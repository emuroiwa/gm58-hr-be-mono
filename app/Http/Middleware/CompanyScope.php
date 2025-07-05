<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\CompanyUser;

class CompanyScope
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get company from header or user's default company
        $companyId = $request->header('X-Company-ID');
        
        if (!$companyId) {
            $defaultCompany = $user->companies()->where('is_default', true)->first();
            $companyId = $defaultCompany?->company_id;
        }

        if (!$companyId) {
            return response()->json(['error' => 'No company context'], 400);
        }

        // Verify user has access to this company
        $companyUser = CompanyUser::where('user_id', $user->id)
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->first();

        if (!$companyUser) {
            return response()->json(['error' => 'Access denied to company'], 403);
        }

        // Add company context to request
        $request->merge(['company_id' => $companyId]);
        $request->merge(['company_role' => $companyUser->role]);
        
        // Set current company on user for easy access
        $user->currentCompany = $companyUser->company;
        $user->currentCompanyRole = $companyUser->role;

        return $next($request);
    }
}

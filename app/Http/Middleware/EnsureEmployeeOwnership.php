<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Employee;

class EnsureEmployeeOwnership
{
    /**
     * Handle an incoming request.
     * Ensures employees can only access their own data.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user || !$user->employee) {
            return response()->json(['message' => 'Employee record not found'], 404);
        }

        // Extract employee ID from route
        $routeEmployeeId = $request->route('employee') ?? 
                          $request->route('id') ?? 
                          $request->input('employee_id');

        // If no employee ID in route, add current user's employee ID
        if (!$routeEmployeeId) {
            $request->merge(['employee_id' => $user->employee->id]);
            return $next($request);
        }

        // Super admins and admins can access any employee data
        if (in_array($user->role, ['super_admin', 'admin', 'hr'])) {
            return $next($request);
        }

        // Managers can access their subordinates' data
        if ($user->role === 'manager') {
            $employee = Employee::find($routeEmployeeId);
            if ($employee && $employee->manager_id === $user->employee->id) {
                return $next($request);
            }
        }

        // Regular employees can only access their own data
        if ($user->role === 'employee') {
            if ($routeEmployeeId !== $user->employee->id) {
                return response()->json([
                    'message' => 'You can only access your own data',
                    'code' => 'UNAUTHORIZED_ACCESS'
                ], 403);
            }
        }

        return $next($request);
    }
}

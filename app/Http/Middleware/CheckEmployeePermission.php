<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployeePermission
{
    /**
     * Handle an incoming request.
     * Checks if user has required permissions for the requested action.
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Ensure user has a role
        if (!$user->role) {
            return response()->json(['message' => 'User role not assigned'], 403);
        }

        // Super admin has all permissions
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // Company admin has most permissions within their company
        if ($user->role === 'admin') {
            // Admins can do most things except delete company or change billing
            $restrictedActions = ['company.delete', 'billing.manage'];
            if ($permission && in_array($permission, $restrictedActions)) {
                return response()->json(['message' => 'Insufficient permissions'], 403);
            }
            return $next($request);
        }

        // HR role permissions
        if ($user->role === 'hr') {
            $allowedPermissions = [
                'employees.view', 'employees.create', 'employees.update',
                'payroll.view', 'payroll.process',
                'attendance.view', 'attendance.manage',
                'leaves.view', 'leaves.approve',
                'reports.view'
            ];
            
            if ($permission && !in_array($permission, $allowedPermissions)) {
                return response()->json(['message' => 'Insufficient permissions'], 403);
            }
            return $next($request);
        }

        // Manager role permissions
        if ($user->role === 'manager') {
            $allowedPermissions = [
                'employees.view', 'employees.update',
                'attendance.view',
                'leaves.view', 'leaves.approve',
                'reports.view',
                'timesheets.approve'
            ];
            
            if ($permission && !in_array($permission, $allowedPermissions)) {
                return response()->json(['message' => 'Insufficient permissions'], 403);
            }
            return $next($request);
        }

        // Employee role permissions (most restrictive)
        if ($user->role === 'employee') {
            $allowedPermissions = [
                'profile.view', 'profile.update',
                'attendance.own', 'leaves.own',
                'timesheets.own', 'payroll.own'
            ];
            
            if ($permission && !in_array($permission, $allowedPermissions)) {
                return response()->json(['message' => 'Insufficient permissions'], 403);
            }
            
            // Employees can only access their own data
            $this->checkOwnDataAccess($request, $user);
            
            return $next($request);
        }

        return response()->json(['message' => 'Invalid user role'], 403);
    }

    /**
     * Check if employee is accessing their own data
     */
    private function checkOwnDataAccess(Request $request, $user)
    {
        // Extract employee ID from route parameters
        $routeEmployeeId = $request->route('employee') ?? $request->route('id');
        
        if ($routeEmployeeId && $user->employee_id !== $routeEmployeeId) {
            abort(403, 'You can only access your own data');
        }
    }
}

<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// User-specific notifications channel
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Company-wide announcements channel
Broadcast::channel('company.{companyId}', function ($user, $companyId) {
    return $user->company_id === (int) $companyId;
});

// Department-specific channel
Broadcast::channel('department.{departmentId}', function ($user, $departmentId) {
    return $user->employee && $user->employee->department_id === (int) $departmentId;
});

// HR team channel
Broadcast::channel('hr.{companyId}', function ($user, $companyId) {
    return $user->company_id === (int) $companyId && 
           in_array($user->role, ['admin', 'hr']);
});

// Manager channel for team updates
Broadcast::channel('manager.{managerId}', function ($user, $managerId) {
    return $user->employee && 
           ($user->employee->id === (int) $managerId || 
            $user->employee->manager_id === (int) $managerId);
});

// Payroll processing channel
Broadcast::channel('payroll.{companyId}', function ($user, $companyId) {
    return $user->company_id === (int) $companyId && 
           in_array($user->role, ['admin', 'hr']);
});

// Attendance updates channel
Broadcast::channel('attendance.{companyId}', function ($user, $companyId) {
    return $user->company_id === (int) $companyId;
});

// Leave approval channel
Broadcast::channel('leave-approval.{companyId}', function ($user, $companyId) {
    return $user->company_id === (int) $companyId && 
           in_array($user->role, ['admin', 'hr', 'manager']);
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\PayrollController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\DepartmentController;
use App\Http\Controllers\Api\V1\PositionController;
use App\Http\Controllers\Api\V1\AttendanceController;
use App\Http\Controllers\Api\V1\LeaveController;
use App\Http\Controllers\Api\V1\TimeSheetController;
use App\Http\Controllers\Api\V1\PerformanceController;
use App\Http\Controllers\Api\V1\TrainingController;
use App\Http\Controllers\Api\V1\BenefitController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Web\FileController;
use App\Http\Controllers\Api\V1\SettingsController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\SystemController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// API Version 1 Routes
Route::prefix('v1')->group(function () {
    
    // Public Authentication Routes
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register-company', [AuthController::class, 'registerCompany']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
        Route::post('verify-email', [AuthController::class, 'verifyEmail']);
    });

    // Protected Routes - Require Authentication
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // Authentication Management
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refreshToken']);
            Route::get('me', [AuthController::class, 'me']);
            Route::put('profile', [AuthController::class, 'updateProfile']);
            Route::put('change-password', [AuthController::class, 'changePassword']);
        });

        // Dashboard Routes
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('dashboard/stats', [DashboardController::class, 'getStats']);
        Route::get('dashboard/recent-activities', [DashboardController::class, 'getRecentActivities']);

        // Company Management Routes
        Route::prefix('company')->group(function () {
            Route::get('/', [CompanyController::class, 'show']);
            Route::put('/', [CompanyController::class, 'update'])->middleware('employee.permission:company.update');
            Route::post('logo', [CompanyController::class, 'uploadLogo'])->middleware('employee.permission:company.update');
            Route::get('settings', [CompanyController::class, 'getSettings']);
            Route::put('settings', [CompanyController::class, 'updateSettings'])->middleware('employee.permission:company.update');
        });

        // Department Management Routes
        Route::prefix('departments')->middleware('employee.permission:departments.view')->group(function () {
            Route::get('/', [DepartmentController::class, 'index']);
            Route::post('/', [DepartmentController::class, 'store'])->middleware('employee.permission:departments.create');
            Route::get('{department}', [DepartmentController::class, 'show']);
            Route::put('{department}', [DepartmentController::class, 'update'])->middleware('employee.permission:departments.update');
            Route::delete('{department}', [DepartmentController::class, 'destroy'])->middleware('employee.permission:departments.delete');
            Route::get('{department}/employees', [DepartmentController::class, 'getEmployees']);
        });

        // Position Management Routes
        Route::prefix('positions')->middleware('employee.permission:positions.view')->group(function () {
            Route::get('/', [PositionController::class, 'index']);
            Route::post('/', [PositionController::class, 'store'])->middleware('employee.permission:positions.create');
            Route::get('{position}', [PositionController::class, 'show']);
            Route::put('{position}', [PositionController::class, 'update'])->middleware('employee.permission:positions.update');
            Route::delete('{position}', [PositionController::class, 'destroy'])->middleware('employee.permission:positions.delete');
        });

        // Employee Management Routes
        Route::prefix('employees')->group(function () {
            Route::get('/', [EmployeeController::class, 'index'])->middleware('employee.permission:employees.view');
            Route::post('/', [EmployeeController::class, 'store'])->middleware('employee.permission:employees.create');
            Route::get('export', [EmployeeController::class, 'export'])->middleware('employee.permission:employees.export');
            Route::post('import', [EmployeeController::class, 'import'])->middleware('employee.permission:employees.import');
            
            Route::prefix('{employee}')->group(function () {
                Route::get('/', [EmployeeController::class, 'show'])->middleware('employee.ownership');
                Route::put('/', [EmployeeController::class, 'update'])->middleware('employee.permission:employees.update');
                Route::delete('/', [EmployeeController::class, 'destroy'])->middleware('employee.permission:employees.delete');
                Route::post('avatar', [EmployeeController::class, 'uploadAvatar'])->middleware('employee.ownership');
                Route::get('documents', [EmployeeController::class, 'getDocuments'])->middleware('employee.ownership');
                Route::post('documents', [EmployeeController::class, 'uploadDocument'])->middleware('employee.ownership');
                Route::delete('documents/{document}', [EmployeeController::class, 'deleteDocument'])->middleware('employee.ownership');
            });
        });

        // Payroll Management Routes
        Route::prefix('payroll')->middleware('employee.permission:payroll.view')->group(function () {
            Route::prefix('periods')->group(function () {
                Route::get('/', [PayrollController::class, 'getPeriods']);
                Route::post('/', [PayrollController::class, 'createPeriod'])->middleware('employee.permission:payroll.create');
                Route::get('{period}', [PayrollController::class, 'showPeriod']);
                Route::put('{period}', [PayrollController::class, 'updatePeriod'])->middleware('employee.permission:payroll.update');
                Route::post('{period}/process', [PayrollController::class, 'processPeriod'])->middleware('employee.permission:payroll.process');
                Route::get('{period}/payrolls', [PayrollController::class, 'getPeriodPayrolls']);
                Route::get('{period}/export', [PayrollController::class, 'exportPeriod'])->middleware('employee.permission:payroll.export');
            });
            
            Route::prefix('payrolls')->group(function () {
                Route::get('/', [PayrollController::class, 'index']);
                Route::get('{payroll}', [PayrollController::class, 'show']);
                Route::put('{payroll}', [PayrollController::class, 'update'])->middleware('employee.permission:payroll.update');
                Route::get('{payroll}/slip', [PayrollController::class, 'getPaySlip']);
                Route::get('{payroll}/slip/download', [PayrollController::class, 'downloadPaySlip']);
            });

            // Employee Payroll Routes (for employees to view their own)
            Route::prefix('employee/{employee}')->middleware('employee.ownership')->group(function () {
                Route::get('payrolls', [PayrollController::class, 'getEmployeePayrolls']);
                Route::get('payrolls/{payroll}', [PayrollController::class, 'getEmployeePayroll']);
            });
        });

        // Attendance Management Routes
        Route::prefix('attendance')->group(function () {
            Route::get('/', [AttendanceController::class, 'index'])->middleware('employee.permission:attendance.view');
            Route::post('checkin', [AttendanceController::class, 'checkIn']);
            Route::post('checkout', [AttendanceController::class, 'checkOut']);
            Route::get('today', [AttendanceController::class, 'getTodayAttendance']);
            Route::get('employee/{employee}', [AttendanceController::class, 'getEmployeeAttendance'])->middleware('employee.ownership');
            Route::post('/', [AttendanceController::class, 'store'])->middleware('employee.permission:attendance.manage');
            Route::put('{attendance}', [AttendanceController::class, 'update'])->middleware('employee.permission:attendance.manage');
            Route::delete('{attendance}', [AttendanceController::class, 'destroy'])->middleware('employee.permission:attendance.manage');
            Route::get('report', [AttendanceController::class, 'getReport'])->middleware('employee.permission:attendance.view');
            Route::get('export', [AttendanceController::class, 'export'])->middleware('employee.permission:attendance.export');
        });

        // Leave Management Routes
        Route::prefix('leaves')->group(function () {
            Route::get('/', [LeaveController::class, 'index'])->middleware('employee.permission:leaves.view');
            Route::post('/', [LeaveController::class, 'store']);
            Route::get('types', [LeaveController::class, 'getLeaveTypes']);
            Route::get('balance', [LeaveController::class, 'getLeaveBalance']);
            Route::get('balance/employee/{employee}', [LeaveController::class, 'getEmployeeLeaveBalance'])->middleware('employee.ownership');
            
            Route::prefix('{leave}')->group(function () {
                Route::get('/', [LeaveController::class, 'show']);
                Route::put('/', [LeaveController::class, 'update'])->middleware('employee.ownership');
                Route::delete('/', [LeaveController::class, 'destroy'])->middleware('employee.ownership');
                Route::post('approve', [LeaveController::class, 'approve'])->middleware('employee.permission:leaves.approve');
                Route::post('reject', [LeaveController::class, 'reject'])->middleware('employee.permission:leaves.approve');
            });

            // Leave Types Management (Admin only)
            Route::prefix('types')->middleware('employee.permission:leave_types.manage')->group(function () {
                Route::post('/', [LeaveController::class, 'createLeaveType']);
                Route::put('{leaveType}', [LeaveController::class, 'updateLeaveType']);
                Route::delete('{leaveType}', [LeaveController::class, 'deleteLeaveType']);
            });
        });

        // TimeSheet Management Routes
        Route::prefix('timesheets')->group(function () {
            Route::get('/', [TimeSheetController::class, 'index']);
            Route::post('/', [TimeSheetController::class, 'store']);
            Route::get('employee/{employee}', [TimeSheetController::class, 'getEmployeeTimeSheets'])->middleware('employee.ownership');
            Route::post('submit', [TimeSheetController::class, 'submit']);
            
            Route::prefix('{timesheet}')->group(function () {
                Route::get('/', [TimeSheetController::class, 'show']);
                Route::put('/', [TimeSheetController::class, 'update'])->middleware('employee.ownership');
                Route::delete('/', [TimeSheetController::class, 'destroy'])->middleware('employee.ownership');
                Route::post('approve', [TimeSheetController::class, 'approve'])->middleware('employee.permission:timesheets.approve');
                Route::post('reject', [TimeSheetController::class, 'reject'])->middleware('employee.permission:timesheets.approve');
            });
        });

        // Performance Management Routes
        Route::prefix('performance')->middleware('employee.permission:performance.view')->group(function () {
            Route::get('/', [PerformanceController::class, 'index']);
            Route::post('/', [PerformanceController::class, 'store'])->middleware('employee.permission:performance.create');
            Route::get('employee/{employee}', [PerformanceController::class, 'getEmployeeReviews']);
            
            Route::prefix('{performance}')->group(function () {
                Route::get('/', [PerformanceController::class, 'show']);
                Route::put('/', [PerformanceController::class, 'update'])->middleware('employee.permission:performance.update');
                Route::delete('/', [PerformanceController::class, 'destroy'])->middleware('employee.permission:performance.delete');
                Route::post('submit', [PerformanceController::class, 'submit']);
                Route::post('approve', [PerformanceController::class, 'approve'])->middleware('employee.permission:performance.approve');
            });
        });

        // Training Management Routes
        Route::prefix('trainings')->middleware('employee.permission:training.view')->group(function () {
            Route::get('/', [TrainingController::class, 'index']);
            Route::post('/', [TrainingController::class, 'store'])->middleware('employee.permission:training.create');
            Route::get('employee/{employee}', [TrainingController::class, 'getEmployeeTrainings']);
            
            Route::prefix('{training}')->group(function () {
                Route::get('/', [TrainingController::class, 'show']);
                Route::put('/', [TrainingController::class, 'update'])->middleware('employee.permission:training.update');
                Route::delete('/', [TrainingController::class, 'destroy'])->middleware('employee.permission:training.delete');
                Route::post('enroll', [TrainingController::class, 'enroll']);
                Route::post('complete', [TrainingController::class, 'markComplete']);
            });
        });

        // Benefits Management Routes
        Route::prefix('benefits')->middleware('employee.permission:benefits.view')->group(function () {
            Route::get('/', [BenefitController::class, 'index']);
            Route::post('/', [BenefitController::class, 'store'])->middleware('employee.permission:benefits.create');
            Route::get('employee/{employee}', [BenefitController::class, 'getEmployeeBenefits'])->middleware('employee.ownership');
            
            Route::prefix('{benefit}')->group(function () {
                Route::get('/', [BenefitController::class, 'show']);
                Route::put('/', [BenefitController::class, 'update'])->middleware('employee.permission:benefits.update');
                Route::delete('/', [BenefitController::class, 'destroy'])->middleware('employee.permission:benefits.delete');
                Route::post('enroll', [BenefitController::class, 'enroll']);
                Route::post('unenroll', [BenefitController::class, 'unenroll']);
            });
        });

        // Reports Routes
        Route::prefix('reports')->middleware('employee.permission:reports.view')->group(function () {
            Route::get('dashboard', [ReportController::class, 'getDashboardReport']);
            Route::get('employees', [ReportController::class, 'getEmployeeReport']);
            Route::get('attendance', [ReportController::class, 'getAttendanceReport']);
            Route::get('payroll', [ReportController::class, 'getPayrollReport']);
            Route::get('leaves', [ReportController::class, 'getLeaveReport']);
            Route::get('performance', [ReportController::class, 'getPerformanceReport']);
            Route::post('generate', [ReportController::class, 'generateReport']);
            Route::get('downloads', [ReportController::class, 'getDownloads']);
            Route::get('download/{file}', [ReportController::class, 'downloadReport']);
        });

        // Notifications Routes
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('unread', [NotificationController::class, 'getUnread']);
            Route::post('{notification}/mark-read', [NotificationController::class, 'markAsRead']);
            Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead']);
            Route::delete('{notification}', [NotificationController::class, 'destroy']);
            Route::get('settings', [NotificationController::class, 'getSettings']);
            Route::put('settings', [NotificationController::class, 'updateSettings']);
        });

        // File Management Routes
        Route::prefix('files')->group(function () {
            Route::post('upload', [FileController::class, 'upload']);
            Route::get('{file}/download', [FileController::class, 'download']);
            Route::delete('{file}', [FileController::class, 'delete']);
        });

        // Settings Routes
        Route::prefix('settings')->middleware('employee.permission:settings.manage')->group(function () {
            Route::get('/', [SettingsController::class, 'index']);
            Route::put('/', [SettingsController::class, 'update']);
            Route::get('currencies', [SettingsController::class, 'getCurrencies']);
            Route::get('timezones', [SettingsController::class, 'getTimezones']);
        });

        // User Management Routes (Admin only)
        Route::prefix('users')->middleware('employee.permission:users.manage')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::post('/', [UserController::class, 'store']);
            Route::get('{user}', [UserController::class, 'show']);
            Route::put('{user}', [UserController::class, 'update']);
            Route::delete('{user}', [UserController::class, 'destroy']);
            Route::post('{user}/activate', [UserController::class, 'activate']);
            Route::post('{user}/deactivate', [UserController::class, 'deactivate']);
            Route::post('{user}/reset-password', [UserController::class, 'resetPassword']);
        });

        // Backup and Maintenance Routes (Super Admin only)
        Route::prefix('system')->middleware('employee.permission:system.manage')->group(function () {
            Route::post('backup', [SystemController::class, 'createBackup']);
            Route::get('backups', [SystemController::class, 'getBackups']);
            Route::get('logs', [SystemController::class, 'getLogs']);
            Route::post('maintenance', [SystemController::class, 'toggleMaintenance']);
            Route::get('health', [SystemController::class, 'healthCheck']);
        });
    });
});

// Fallback route for API
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found',
        'status' => 404
    ], 404);
});

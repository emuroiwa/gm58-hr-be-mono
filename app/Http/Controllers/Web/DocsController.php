<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DocsController extends Controller
{
    /**
     * Show documentation home
     */
    public function index(): View
    {
        $sections = [
            [
                'title' => 'Getting Started',
                'description' => 'Quick setup guide for new users',
                'url' => '/docs/getting-started',
                'icon' => 'play-circle'
            ],
            [
                'title' => 'API Documentation',
                'description' => 'Complete API reference and examples',
                'url' => '/docs/api',
                'icon' => 'code'
            ],
            [
                'title' => 'Authentication',
                'description' => 'User management and security',
                'url' => '/docs/authentication',
                'icon' => 'shield'
            ],
            [
                'title' => 'Employee Management',
                'description' => 'Managing employee records and data',
                'url' => '/docs/employees',
                'icon' => 'users'
            ],
            [
                'title' => 'Payroll System',
                'description' => 'Payroll processing and calculations',
                'url' => '/docs/payroll',
                'icon' => 'credit-card'
            ],
            [
                'title' => 'Attendance Tracking',
                'description' => 'Time and attendance management',
                'url' => '/docs/attendance',
                'icon' => 'clock'
            ]
        ];

        return view('docs.index', compact('sections'));
    }

    /**
     * Show API documentation
     */
    public function api(): View
    {
        $endpoints = [
            'Authentication' => [
                ['method' => 'POST', 'endpoint' => '/api/v1/auth/login', 'description' => 'User login'],
                ['method' => 'POST', 'endpoint' => '/api/v1/auth/register-company', 'description' => 'Company registration'],
                ['method' => 'POST', 'endpoint' => '/api/v1/auth/logout', 'description' => 'User logout'],
            ],
            'Employees' => [
                ['method' => 'GET', 'endpoint' => '/api/v1/employees', 'description' => 'List employees'],
                ['method' => 'POST', 'endpoint' => '/api/v1/employees', 'description' => 'Create employee'],
                ['method' => 'PUT', 'endpoint' => '/api/v1/employees/{id}', 'description' => 'Update employee'],
                ['method' => 'DELETE', 'endpoint' => '/api/v1/employees/{id}', 'description' => 'Delete employee'],
            ],
            'Payroll' => [
                ['method' => 'GET', 'endpoint' => '/api/v1/payroll/periods', 'description' => 'List payroll periods'],
                ['method' => 'POST', 'endpoint' => '/api/v1/payroll/periods/{id}/process', 'description' => 'Process payroll'],
            ],
            'Attendance' => [
                ['method' => 'POST', 'endpoint' => '/api/v1/attendance/checkin', 'description' => 'Clock in'],
                ['method' => 'POST', 'endpoint' => '/api/v1/attendance/checkout', 'description' => 'Clock out'],
                ['method' => 'GET', 'endpoint' => '/api/v1/attendance', 'description' => 'Get attendance records'],
            ]
        ];

        return view('docs.api', compact('endpoints'));
    }

    /**
     * Show getting started guide
     */
    public function gettingStarted(): View
    {
        return view('docs.getting-started');
    }

    /**
     * Show authentication docs
     */
    public function authentication(): View
    {
        return view('docs.authentication');
    }

    /**
     * Show employee management docs
     */
    public function employees(): View
    {
        return view('docs.employees');
    }

    /**
     * Show payroll docs
     */
    public function payroll(): View
    {
        return view('docs.payroll');
    }

    /**
     * Show attendance docs
     */
    public function attendance(): View
    {
        return view('docs.attendance');
    }
}

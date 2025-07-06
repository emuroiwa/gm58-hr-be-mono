@extends('layouts.app')

@section('title', 'Authentication')

@section('content')
<div class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-12">
            <nav class="text-sm breadcrumbs mb-4">
                <a href="{{ url('/docs') }}" class="text-blue-600 hover:text-blue-500">Documentation</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-500">Authentication</span>
            </nav>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Authentication & Security</h1>
            <p class="text-xl text-gray-600">Learn how user authentication and security features work in the HR system.</p>
        </div>

        <!-- Content sections -->
        <div class="space-y-12">
            <!-- User Roles -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">User Roles & Permissions</h2>
                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Super Admin</h3>
                        <p class="text-gray-600 mb-4">Complete system access with full administrative privileges.</p>
                        <div class="bg-gray-50 rounded p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Permissions:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Manage all users and companies</li>
                                <li>• Access system-wide settings</li>
                                <li>• View all data and reports</li>
                                <li>• Manage billing and subscriptions</li>
                            </ul>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Company Admin</h3>
                        <p class="text-gray-600 mb-4">Full access within their company's data and settings.</p>
                        <div class="bg-gray-50 rounded p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Permissions:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Manage company employees</li>
                                <li>• Configure company settings</li>
                                <li>• Process payroll</li>
                                <li>• Generate reports</li>
                                <li>• Manage departments and positions</li>
                            </ul>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">HR Manager</h3>
                        <p class="text-gray-600 mb-4">HR-specific permissions for employee management and reporting.</p>
                        <div class="bg-gray-50 rounded p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Permissions:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Manage employee records</li>
                                <li>• Approve leave requests</li>
                                <li>• View attendance reports</li>
                                <li>• Process basic payroll tasks</li>
                            </ul>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Employee</h3>
                        <p class="text-gray-600 mb-4">Basic access to view personal information and submit requests.</p>
                        <div class="bg-gray-50 rounded p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Permissions:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• View personal profile</li>
                                <li>• Submit leave requests</li>
                                <li>• Clock in/out for attendance</li>
                                <li>• View payslips</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Login Methods -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Login Methods</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Email & Password</h3>
                        <p class="text-gray-600 mb-4">Standard authentication using email address and password.</p>
                        <div class="bg-blue-50 rounded p-4">
                            <h4 class="font-medium text-blue-900 mb-2">Requirements:</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Valid email address</li>
                                <li>• Password minimum 8 characters</li>
                                <li>• Must include uppercase, lowercase, and number</li>
                            </ul>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Two-Factor Authentication</h3>
                        <p class="text-gray-600 mb-4">Enhanced security with SMS or authenticator app verification.</p>
                        <div class="bg-green-50 rounded p-4">
                            <h4 class="font-medium text-green-900 mb-2">Options:</h4>
                            <ul class="text-sm text-green-700 space-y-1">
                                <li>• SMS verification codes</li>
                                <li>• Google Authenticator</li>
                                <li>• Authy or similar apps</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Security -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Password Security</h2>
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Password Requirements</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Minimum Requirements:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• At least 8 characters long</li>
                                <li>• One uppercase letter (A-Z)</li>
                                <li>• One lowercase letter (a-z)</li>
                                <li>• One number (0-9)</li>
                                <li>• One special character (!@#$%^&*)</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Security Features:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Passwords are encrypted using bcrypt</li>
                                <li>• Account lockout after 5 failed attempts</li>
                                <li>• Password reset via secure email link</li>
                                <li>• Regular password change reminders</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Session Management -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Session Management</h2>
                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Session Duration</h3>
                        <p class="text-gray-600 mb-4">Sessions are automatically managed for security and convenience.</p>
                        <div class="bg-yellow-50 rounded p-4">
                            <ul class="text-sm text-yellow-800 space-y-1">
                                <li>• Standard sessions: 8 hours of inactivity</li>
                                <li>• "Remember Me": 30 days (secure devices only)</li>
                                <li>• Automatic logout for security</li>
                                <li>• Manual logout available anytime</li>
                            </ul>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Active Sessions</h3>
                        <p class="text-gray-600 mb-4">Users can view and manage their active sessions across devices.</p>
                        <div class="bg-blue-50 rounded p-4">
                            <h4 class="font-medium text-blue-900 mb-2">Session Information Includes:</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Device type and browser</li>
                                <li>• Login location (IP address)</li>
                                <li>• Last activity timestamp</li>
                                <li>• Option to terminate specific sessions</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Authentication -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">API Authentication</h2>
                <p class="text-gray-600 mb-6">For developers integrating with the HR system API.</p>
                
                <div class="bg-gray-900 rounded-lg p-6 mb-6">
                    <h3 class="text-white font-semibold mb-3">Bearer Token Authentication</h3>
                    <pre class="text-green-400 text-sm overflow-x-auto"><code>curl -H "Authorization: Bearer your-api-token" \
     -H "Content-Type: application/json" \
     {{ config('app.url') }}/api/v1/employees</code></pre>
                </div>

                <div class="border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">API Token Management</h3>
                    <ul class="text-gray-600 space-y-2">
                        <li>• Tokens can be generated in user settings</li>
                        <li>• Each token has configurable expiration</li>
                        <li>• Tokens can be revoked immediately</li>
                        <li>• Rate limiting applies to API requests</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

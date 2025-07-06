@extends('layouts.app')

@section('title', 'Getting Started')

@section('content')
<div class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-12">
            <nav class="text-sm breadcrumbs mb-4">
                <a href="{{ url('/docs') }}" class="text-blue-600 hover:text-blue-500">Documentation</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-500">Getting Started</span>
            </nav>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Getting Started</h1>
            <p class="text-xl text-gray-600">Quick setup guide to get your HR system up and running in minutes.</p>
        </div>

        <!-- Step-by-step guide -->
        <div class="space-y-12">
            <!-- Step 1 -->
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white font-bold">1</div>
                </div>
                <div class="ml-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Create Your Account</h2>
                    <p class="text-gray-600 mb-4">Start by creating your company account. You'll need basic information about your organization.</p>
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Required Information:</h3>
                        <ul class="list-disc list-inside text-gray-600 space-y-1">
                            <li>Company name and address</li>
                            <li>Admin user details (name, email, password)</li>
                            <li>Industry type</li>
                            <li>Number of employees (estimate)</li>
                        </ul>
                    </div>
                    <a href="{{ route('auth.register') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Create Account
                    </a>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white font-bold">2</div>
                </div>
                <div class="ml-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Complete Company Setup</h2>
                    <p class="text-gray-600 mb-4">Configure your company settings including departments, positions, and leave policies.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h3 class="font-semibold text-blue-900 mb-2">Departments</h3>
                            <p class="text-blue-700 text-sm">Set up your organizational structure</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <h3 class="font-semibold text-green-900 mb-2">Positions</h3>
                            <p class="text-green-700 text-sm">Define job roles and responsibilities</p>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <h3 class="font-semibold text-yellow-900 mb-2">Leave Policies</h3>
                            <p class="text-yellow-700 text-sm">Configure vacation and sick leave rules</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <h3 class="font-semibold text-purple-900 mb-2">Pay Schedules</h3>
                            <p class="text-purple-700 text-sm">Set up payroll frequencies</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white font-bold">3</div>
                </div>
                <div class="ml-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Add Your Employees</h2>
                    <p class="text-gray-600 mb-4">Import existing employee data or add employees manually to get started.</p>
                    <div class="space-y-4">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-2">Bulk Import (Recommended)</h3>
                            <p class="text-gray-600 text-sm mb-3">Upload a CSV file with all your employee data</p>
                            <button class="inline-flex items-center px-3 py-2 border border-blue-600 text-sm font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50">
                                Download CSV Template
                            </button>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-2">Manual Entry</h3>
                            <p class="text-gray-600 text-sm mb-3">Add employees one by one through the interface</p>
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Add Employee
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white font-bold">4</div>
                </div>
                <div class="ml-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Configure Payroll</h2>
                    <p class="text-gray-600 mb-4">Set up payroll calculations, tax settings, and bank account information.</p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Important</h3>
                                <p class="mt-1 text-sm text-yellow-700">Ensure you have the necessary tax information and bank details before setting up payroll.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-green-600 text-white font-bold">✓</div>
                </div>
                <div class="ml-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">You're All Set!</h2>
                    <p class="text-gray-600 mb-4">Your HR system is now ready to use. Here are some next steps to explore:</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ url('/docs/employees') }}" class="block p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition-all">
                            <h3 class="font-semibold text-gray-900 mb-2">Employee Management</h3>
                            <p class="text-gray-600 text-sm">Learn how to manage employee records, documents, and profiles</p>
                        </a>
                        <a href="{{ url('/docs/attendance') }}" class="block p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition-all">
                            <h3 class="font-semibold text-gray-900 mb-2">Time Tracking</h3>
                            <p class="text-gray-600 text-sm">Set up attendance tracking and time management</p>
                        </a>
                        <a href="{{ url('/docs/payroll') }}" class="block p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition-all">
                            <h3 class="font-semibold text-gray-900 mb-2">Payroll Processing</h3>
                            <p class="text-gray-600 text-sm">Learn about payroll calculations and processing</p>
                        </a>
                        <a href="{{ url('/docs/api') }}" class="block p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition-all">
                            <h3 class="font-semibold text-gray-900 mb-2">API Integration</h3>
                            <p class="text-gray-600 text-sm">Integrate with other systems using our API</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support section -->
        <div class="mt-16 bg-blue-50 rounded-lg p-8">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Need Help?</h2>
                <p class="text-gray-600 mb-6">Our support team is here to help you get started successfully.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ url('/contact') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Contact Support
                    </a>
                    <a href="{{ url('/docs') }}" class="inline-flex items-center justify-center px-6 py-3 border border-blue-600 text-base font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50">
                        Browse Documentation
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

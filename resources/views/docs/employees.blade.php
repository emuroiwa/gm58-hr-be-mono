@extends('layouts.app')

@section('title', 'Employee Management')

@section('content')
<div class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-12">
            <nav class="text-sm breadcrumbs mb-4">
                <a href="{{ url('/docs') }}" class="text-blue-600 hover:text-blue-500">Documentation</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-500">Employee Management</span>
            </nav>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Employee Management</h1>
            <p class="text-xl text-gray-600">Complete guide to managing employee records, profiles, and information.</p>
        </div>

        <div class="space-y-12">
            <!-- Adding Employees -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Adding Employees</h2>
                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Individual Employee Entry</h3>
                        <p class="text-gray-600 mb-4">Add employees one at a time through the web interface.</p>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="font-medium text-blue-900 mb-2">Required Information:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-blue-800">
                                <ul class="space-y-1">
                                    <li>• Full name</li>
                                    <li>• Email address</li>
                                    <li>• Phone number</li>
                                    <li>• Hire date</li>
                                </ul>
                                <ul class="space-y-1">
                                    <li>• Department</li>
                                    <li>• Position/Job title</li>
                                    <li>• Employment type</li>
                                    <li>• Salary information</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bulk Import via CSV</h3>
                        <p class="text-gray-600 mb-4">Upload multiple employees at once using a CSV file.</p>
                        <div class="bg-green-50 rounded-lg p-4 mb-4">
                            <h4 class="font-medium text-green-900 mb-2">CSV Format Requirements:</h4>
                            <ul class="text-sm text-green-800 space-y-1">
                                <li>• First row must contain column headers</li>
                                <li>• Date format: YYYY-MM-DD</li>
                                <li>• Email addresses must be unique</li>
                                <li>• Employee IDs will be auto-generated if not provided</li>
                            </ul>
                        </div>
                        <button class="inline-flex items-center px-4 py-2 border border-green-600 text-sm font-medium rounded-md text-green-600 bg-white hover:bg-green-50">
                            Download CSV Template
                        </button>
                    </div>
                </div>
            </div>

            <!-- Employee Profiles -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Employee Profiles</h2>
                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Basic Details:</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Full name and preferred name</li>
                                    <li>• Contact information</li>
                                    <li>• Emergency contacts</li>
                                    <li>• Address and location</li>
                                    <li>• Date of birth</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Employment Details:</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Employee ID</li>
                                    <li>• Hire date and anniversary</li>
                                    <li>• Department and team</li>
                                    <li>• Job title and level</li>
                                    <li>• Manager and direct reports</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Picture & Documents</h3>
                        <p class="text-gray-600 mb-4">Employees can upload profile pictures and important documents.</p>
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <h4 class="font-medium text-yellow-900 mb-2">Supported Documents:</h4>
                            <ul class="text-sm text-yellow-800 space-y-1">
                                <li>• Government-issued ID</li>
                                <li>• Tax forms and certificates</li>
                                <li>• Educational qualifications</li>
                                <li>• Employment contracts</li>
                                <li>• Performance reviews</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Organizational Structure -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Organizational Structure</h2>
                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Departments</h3>
                        <p class="text-gray-600 mb-4">Organize employees into departments for better management.</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4 text-center">
                                <h4 class="font-medium text-blue-900">Engineering</h4>
                                <p class="text-sm text-blue-700 mt-1">Development & Technical</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4 text-center">
                                <h4 class="font-medium text-green-900">Human Resources</h4>
                                <p class="text-sm text-green-700 mt-1">People & Culture</p>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4 text-center">
                                <h4 class="font-medium text-purple-900">Sales & Marketing</h4>
                                <p class="text-sm text-purple-700 mt-1">Revenue & Growth</p>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reporting Relationships</h3>
                        <p class="text-gray-600 mb-4">Define manager-employee relationships and organizational hierarchy.</p>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Features:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Visual organization chart</li>
                                <li>• Manager approval workflows</li>
                                <li>• Direct report management</li>
                                <li>• Cross-department collaboration tracking</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Status Management -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Employee Status Management</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Employment Status</h3>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-900">Active</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-900">On Leave</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-900">Inactive</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-gray-500 rounded-full mr-3"></div>
                                <span class="text-sm font-medium text-gray-900">Terminated</span>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Offboarding Process</h3>
                        <p class="text-gray-600 mb-4">Systematic approach to employee departures.</p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Exit interview scheduling</li>
                            <li>• Final payroll processing</li>
                            <li>• Benefits termination</li>
                            <li>• Equipment return tracking</li>
                            <li>• Access revocation</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Search and Filtering -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Search and Filtering</h2>
                <div class="border border-gray-200 rounded-lg p-6">
                    <p class="text-gray-600 mb-4">Powerful search capabilities to find employees quickly.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Search Options:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Name and contact information</li>
                                <li>• Employee ID or badge number</li>
                                <li>• Department and position</li>
                                <li>• Skills and qualifications</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Filter Criteria:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li>• Employment status</li>
                                <li>• Hire date ranges</li>
                                <li>• Department and team</li>
                                <li>• Location and office</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

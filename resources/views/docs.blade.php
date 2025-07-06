@extends('layouts.app')

@section('title', 'Documentation')

@section('content')
<!-- Hero section -->
<div class="bg-white py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                <span class="block">Documentation</span>
                <span class="block text-blue-600">& Getting Started</span>
            </h1>
            <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                Everything you need to know to get the most out of our HR management platform.
            </p>
        </div>
    </div>
</div>

<!-- Documentation sections -->
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Quick Start -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Start Guide</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-blue-600 hover:text-blue-500">Setting up your account</a></li>
                        <li><a href="#" class="text-blue-600 hover:text-blue-500">Adding your first employee</a></li>
                        <li><a href="#" class="text-blue-600 hover:text-blue-500">Configuring payroll settings</a></li>
                        <li><a href="#" class="text-blue-600 hover:text-blue-500">Setting up leave policies</a></li>
                    </ul>
                </div>
            </div>

            <!-- User Guides -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">User Guides</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-blue-600 hover:text-blue-500">Employee Management</a></li>
                        <li><a href="#" class="text-blue-600 hover:text-blue-500">Payroll Processing</a></li>
                        <li><a href="#" class="text-blue-600 hover:text-blue-500">Time & Attendance</a></li>
                        <li><a href="#" class="text-blue-600 hover:text-blue-500">Performance Reviews</a></li>
                        <li><a href="#" class="text-blue-600 hover:text-blue-500">Leave Management</a></li>
                        <li><a href="#" class="text-blue-600 hover:text-blue-500">Reporting & Analytics</a></li>
                    </ul>
                </div>
            </div>

            <!-- API & Integrations -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">API & Integrations</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-blue-600 hover:text-blue-500">API Documentation</a></li>
                        <li><a href="#" class="text-blue-600 hover:text-blue-500">Authentication</a></li>
                        <li><a href="#" class="text-blue-600 hover:text-blue-500">Webhooks</a></li>
                        <li><a href="#" class="text-blue-600 hover:text-blue-500">Third-party Integrations</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="mt-16">
            <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-8">Frequently Asked Questions</h2>
            <div class="space-y-8">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">How do I get started with the platform?</h3>
                    <p class="text-gray-600">Simply sign up for a free account, complete the setup wizard, and start adding your employees. Our quick start guide will walk you through each step.</p>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Can I import existing employee data?</h3>
                    <p class="text-gray-600">Yes, you can import employee data using our CSV import feature or through our API. We support bulk imports for employee information, payroll data, and attendance records.</p>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Is my data secure?</h3>
                    <p class="text-gray-600">Absolutely. We use enterprise-grade security measures including data encryption, regular security audits, and compliance with industry standards to protect your data.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Support CTA -->
<div class="bg-blue-600">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-white">Need help?</h2>
            <p class="mt-4 text-lg text-blue-100">Our support team is here to help you succeed.</p>
            <div class="mt-8">
                <a href="{{ url('/contact') }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50">
                    Contact Support
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

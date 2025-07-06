@extends('layouts.app')

@section('title', 'Security')

@section('content')
<!-- Hero section -->
<div class="bg-white py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                <span class="block">Enterprise-Grade</span>
                <span class="block text-blue-600">Security</span>
            </h1>
            <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                Your data security is our top priority. Learn about the comprehensive measures we take to protect your sensitive HR information.
            </p>
        </div>
    </div>
</div>

<!-- Security features -->
<div class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Data Encryption -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-green-500 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Data Encryption</h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-500">
                            All data is encrypted in transit using TLS 1.3 and at rest using AES-256 encryption.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Access Controls -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Access Controls</h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-500">
                            Role-based access controls and multi-factor authentication to ensure only authorized users can access data.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Regular Audits -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Security Audits</h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-500">
                            Regular security audits and penetration testing by third-party security experts.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Backup & Recovery -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Backup & Recovery</h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-500">
                            Automated daily backups with point-in-time recovery capabilities across multiple geographic regions.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Monitoring -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-red-500 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">24/7 Monitoring</h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-500">
                            Continuous monitoring for security threats and suspicious activities with real-time alerts.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Compliance -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 bg-indigo-500 rounded-lg flex items-center justify-center">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Compliance</h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-sm text-gray-500">
                            SOC 2 Type II certified and compliant with GDPR, CCPA, and other privacy regulations.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Security details -->
<div class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-12">Our Security Commitment</h2>
        
        <div class="space-y-8">
            <div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Infrastructure Security</h3>
                <p class="text-gray-600">
                    Our platform is hosted on enterprise-grade cloud infrastructure with multiple layers of security. 
                    We use industry-leading providers with robust physical security, network protection, and 
                    infrastructure monitoring.
                </p>
            </div>

            <div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Data Protection</h3>
                <p class="text-gray-600">
                    All sensitive data is encrypted using advanced encryption standards both in transit and at rest. 
                    We employ database encryption, secure key management, and regular security assessments to 
                    ensure your data remains protected.
                </p>
            </div>

            <div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Access Management</h3>
                <p class="text-gray-600">
                    We implement strict access controls with role-based permissions, multi-factor authentication, 
                    and session management. Administrative access is logged and monitored, with regular access reviews 
                    to ensure least-privilege principles.
                </p>
            </div>

            <div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Incident Response</h3>
                <p class="text-gray-600">
                    Our security team maintains a comprehensive incident response plan with 24/7 monitoring 
                    and rapid response capabilities. We conduct regular drills and maintain communication 
                    protocols to ensure quick resolution of any security incidents.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Security contact -->
<div class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-extrabold text-gray-900">Security Questions?</h2>
        <p class="mt-4 text-lg text-gray-600">
            Our security team is available to answer any questions about our security practices.
        </p>
        <div class="mt-8">
            <a href="mailto:security@{{ config('app.name', 'hrmanagement') }}.com" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                Contact Security Team
            </a>
        </div>
    </div>
</div>
@endsection

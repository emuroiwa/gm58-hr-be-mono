@extends('layouts.app')

@section('title', 'Payroll System')

@section('content')
<div class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-12">
            <nav class="text-sm breadcrumbs mb-4">
                <a href="{{ url('/docs') }}" class="text-blue-600 hover:text-blue-500">Documentation</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-500">Payroll System</span>
            </nav>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Payroll System</h1>
            <p class="text-xl text-gray-600">Comprehensive guide to payroll processing, calculations, and management.</p>
        </div>

        <div class="space-y-12">
            <!-- Payroll Setup -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Payroll Setup</h2>
                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pay Periods</h3>
                        <p class="text-gray-600 mb-4">Configure how often employees are paid.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <h4 class="font-medium text-blue-900 mb-2">Weekly</h4>
                                <p class="text-sm text-blue-700">52 pay periods per year</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <h4 class="font-medium text-green-900 mb-2">Bi-weekly</h4>
                                <p class="text-sm text-green-700">26 pay periods per year</p>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-4">
                                <h4 class="font-medium text-yellow-900 mb-2">Semi-monthly</h4>
                                <p class="text-sm text-yellow-700">24 pay periods per year</p>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4">
                                <h4 class="font-medium text-purple-900 mb-2">Monthly</h4>
                                <p class="text-sm text-purple-700">12 pay periods per year</p>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Salary Types</h3>
                        <div class="space-y-4">
                            <div class="border-l-4 border-blue-500 pl-4">
                                <h4 class="font-medium text-gray-900">Salary (Exempt)</h4>
                                <p class="text-sm text-gray-600">Fixed annual salary, typically for management positions</p>
                            </div>
                            <div class="border-l-4 border-green-500 pl-4">
                                <h4 class="font-medium text-gray-900">Hourly (Non-exempt)</h4>
                                <p class="text-sm text-gray-600">Paid per hour worked, eligible for overtime</p>
                            </div>
                            <div class="border-l-4 border-yellow-500 pl-4">
                                <h4 class="font-medium text-gray-900">Commission</h4>
                                <p class="text-sm text-gray-600">Performance-based compensation</p>
                            </div>
                            <div class="border-l-4 border-purple-500 pl-4">
                                <h4 class="font-medium text-gray-900">Contract</h4>
                                <p class="text-sm text-gray-600">Fixed-term or project-based payment</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payroll Processing -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Payroll Processing</h2>
                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Processing Steps</h3>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                                <div class="ml-4">
                                    <h4 class="font-medium text-gray-900">Time Collection</h4>
                                    <p class="text-sm text-gray-600">Gather attendance data and hours worked</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                                <div class="ml-4">
                                    <h4 class="font-medium text-gray-900">Calculate Gross Pay</h4>
                                    <p class="text-sm text-gray-600">Apply rates, overtime, and bonuses</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                                <div class="ml-4">
                                    <h4 class="font-medium text-gray-900">Apply Deductions</h4>
                                    <p class="text-sm text-gray-600">Subtract taxes, benefits, and other deductions</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">4</div>
                                <div class="ml-4">
                                    <h4 class="font-medium text-gray-900">Review & Approve</h4>
                                    <p class="text-sm text-gray-600">Final review before payment processing</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold">5</div>
                                <div class="ml-4">
                                    <h4 class="font-medium text-gray-900">Process Payments</h4>
                                    <p class="text-sm text-gray-600">Direct deposit or check distribution</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Overtime Calculations</h3>
                        <p class="text-gray-600 mb-4">Automatic overtime calculation based on configured rules.</p>
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <h4 class="font-medium text-yellow-900 mb-2">Default Rules:</h4>
                            <ul class="text-sm text-yellow-800 space-y-1">
                                <li>• 1.5x rate for hours over 40 per week</li>
                                <li>• 2x rate for hours over 60 per week</li>
                                <li>• Holiday pay multipliers</li>
                                <li>• Custom overtime rules by department</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deductions and Benefits -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Deductions and Benefits</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tax Deductions</h3>
                        <ul class="text-gray-600 space-y-2">
                            <li>• Federal income tax</li>
                            <li>• State income tax</li>
                            <li>• Social Security (6.2%)</li>
                            <li>• Medicare (1.45%)</li>
                            <li>• State disability insurance</li>
                            <li>• Unemployment insurance</li>
                        </ul>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Benefits Deductions</h3>
                        <ul class="text-gray-600 space-y-2">
                            <li>• Health insurance premiums</li>
                            <li>• Dental and vision insurance</li>
                            <li>• 401(k) contributions</li>
                            <li>• Life insurance premiums</li>
                            <li>• Flexible spending accounts</li>
                            <li>• Parking and transit passes</li>
                        </ul>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pre-tax vs Post-tax</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-green-50 rounded-lg p-4">
                            <h4 class="font-medium text-green-900 mb-2">Pre-tax Deductions</h4>
                            <p class="text-sm text-green-700 mb-2">Reduce taxable income</p>
                            <ul class="text-xs text-green-600 space-y-1">
                                <li>• Health insurance</li>
                                <li>• 401(k) contributions</li>
                                <li>• FSA contributions</li>
                            </ul>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="font-medium text-blue-900 mb-2">Post-tax Deductions</h4>
                            <p class="text-sm text-blue-700 mb-2">Applied after tax calculation</p>
                            <ul class="text-xs text-blue-600 space-y-1">
                                <li>• Roth 401(k) contributions</li>
                                <li>• Life insurance (excess)</li>
                                <li>• Union dues</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payslips and Reporting -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Payslips and Reporting</h2>
                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Digital Payslips</h3>
                        <p class="text-gray-600 mb-4">Secure, paperless payslips accessible to employees online.</p>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Payslip Information:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                <ul class="space-y-1">
                                    <li>• Gross pay breakdown</li>
                                    <li>• Hours worked details</li>
                                    <li>• Overtime calculations</li>
                                    <li>• Tax deductions</li>
                                </ul>
                                <ul class="space-y-1">
                                    <li>• Benefits deductions</li>
                                    <li>• Net pay amount</li>
                                    <li>• Year-to-date totals</li>
                                    <li>• Direct deposit details</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payroll Reports</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Standard Reports:</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Payroll register</li>
                                    <li>• Tax liability reports</li>
                                    <li>• Benefits summary</li>
                                    <li>• Labor cost analysis</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Compliance Reports:</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Quarterly tax forms</li>
                                    <li>• Annual W-2 statements</li>
                                    <li>• 1099 contractor forms</li>
                                    <li>• Audit trail reports</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compliance and Security -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Compliance and Security</h2>
                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tax Compliance</h3>
                        <p class="text-gray-600 mb-4">Automated tax calculations and filing support.</p>
                        <div class="bg-red-50 rounded-lg p-4">
                            <h4 class="font-medium text-red-900 mb-2">Important:</h4>
                            <p class="text-sm text-red-700">Always consult with tax professionals for complex situations and ensure compliance with local tax laws.</p>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Security</h3>
                        <ul class="text-gray-600 space-y-2">
                            <li>• Bank-level encryption for all payroll data</li>
                            <li>• Secure transmission of payment files</li>
                            <li>• Role-based access controls</li>
                            <li>• Audit logs for all payroll activities</li>
                            <li>• Compliance with PCI DSS standards</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

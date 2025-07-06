@extends('layouts.app')

@section('title', 'Attendance Tracking')

@section('content')
<div class="bg-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-12">
            <nav class="text-sm breadcrumbs mb-4">
                <a href="{{ url('/docs') }}" class="text-blue-600 hover:text-blue-500">Documentation</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-gray-500">Attendance Tracking</span>
            </nav>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Attendance Tracking</h1>
            <p class="text-xl text-gray-600">Complete guide to time tracking, attendance management, and scheduling.</p>
        </div>

        <div class="space-y-12">
            <!-- Clock In/Out System -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Clock In/Out System</h2>
                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Time Clock Methods</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <h4 class="font-medium text-blue-900 mb-2">Web Interface</h4>
                                <p class="text-sm text-blue-700 mb-3">Clock in/out through the web portal</p>
                                <ul class="text-xs text-blue-600 space-y-1">
                                    <li>• Simple one-click interface</li>
                                    <li>• IP address tracking</li>
                                    <li>• Browser-based location services</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <h4 class="font-medium text-green-900 mb-2">Mobile App</h4>
                                <p class="text-sm text-green-700 mb-3">Clock in/out from mobile devices</p>
                                <ul class="text-xs text-green-600 space-y-1">
                                    <li>• GPS location verification</li>
                                    <li>• Offline capability</li>
                                    <li>• Push notifications</li>
                                </ul>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-4">
                                <h4 class="font-medium text-yellow-900 mb-2">Biometric Scanner</h4>
                                <p class="text-sm text-yellow-700 mb-3">Fingerprint or facial recognition</p>
                                <ul class="text-xs text-yellow-600 space-y-1">
                                    <li>• High security</li>
                                    <li>• Prevents buddy punching</li>
                                    <li>• Integration with hardware</li>
                                </ul>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4">
                                <h4 class="font-medium text-purple-900 mb-2">Badge/Card Reader</h4>
                                <p class="text-sm text-purple-700 mb-3">RFID or magnetic card system</p>
                                <ul class="text-xs text-purple-600 space-y-1">
                                    <li>• Quick and easy</li>
                                    <li>• Cost-effective</li>
                                    <li>• Works with existing ID cards</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Time Tracking Features</h3>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-500 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="font-medium text-gray-900">Automatic Break Detection</h4>
                                    <p class="text-sm text-gray-600">System automatically detects and records break periods</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-500 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="font-medium text-gray-900">Overtime Alerts</h4>
                                    <p class="text-sm text-gray-600">Real-time notifications when approaching overtime thresholds</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-500 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="font-medium text-gray-900">Time Correction Requests</h4>
                                    <p class="text-sm text-gray-600">Employees can request corrections with manager approval</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scheduling -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Employee Scheduling</h2>
                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Schedule Management</h3>
                        <p class="text-gray-600 mb-4">Create and manage employee work schedules with ease.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Schedule Types:</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Fixed schedules (same every week)</li>
                                    <li>• Rotating schedules</li>
                                    <li>• Flexible/variable schedules</li>
                                    <li>• Split shifts</li>
                                    <li>• On-call schedules</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Scheduling Features:</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Drag-and-drop schedule builder</li>
                                    <li>• Conflict detection</li>
                                    <li>• Automatic coverage suggestions</li>
                                    <li>• Template-based scheduling</li>
                                    <li>• Bulk schedule changes</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Shift Swapping & Coverage</h3>
                        <div class="space-y-4">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <h4 class="font-medium text-blue-900 mb-2">Shift Swapping</h4>
                                <p class="text-sm text-blue-700">Employees can request to swap shifts with colleagues</p>
                                <ul class="text-xs text-blue-600 mt-2 space-y-1">
                                    <li>• Manager approval required</li>
                                    <li>• Automatic qualification checking</li>
                                    <li>• Notification system</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <h4 class="font-medium text-green-900 mb-2">Open Shift Bidding</h4>
                                <p class="text-sm text-green-700">Post open shifts for employees to claim</p>
                                <ul class="text-xs text-green-600 mt-2 space-y-1">
                                    <li>• Priority-based assignment</li>
                                    <li>• Skill-based matching</li>
                                    <li>• Time-limited bidding</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Time Off Management -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Time Off Management</h2>
                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave Types</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-3">
                                <div class="border-l-4 border-blue-500 pl-3">
                                    <h4 class="font-medium text-gray-900">Vacation/PTO</h4>
                                    <p class="text-sm text-gray-600">Paid time off for rest and recreation</p>
                                </div>
                                <div class="border-l-4 border-green-500 pl-3">
                                    <h4 class="font-medium text-gray-900">Sick Leave</h4>
                                    <p class="text-sm text-gray-600">Medical leave for illness or appointments</p>
                                </div>
                                <div class="border-l-4 border-yellow-500 pl-3">
                                    <h4 class="font-medium text-gray-900">Personal Leave</h4>
                                    <p class="text-sm text-gray-600">Personal business or family matters</p>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="border-l-4 border-purple-500 pl-3">
                                    <h4 class="font-medium text-gray-900">Bereavement</h4>
                                    <p class="text-sm text-gray-600">Time off for family loss</p>
                                </div>
                                <div class="border-l-4 border-red-500 pl-3">
                                    <h4 class="font-medium text-gray-900">FMLA</h4>
                                    <p class="text-sm text-gray-600">Family and Medical Leave Act</p>
                                </div>
                                <div class="border-l-4 border-gray-500 pl-3">
                                    <h4 class="font-medium text-gray-900">Jury Duty</h4>
                                    <p class="text-sm text-gray-600">Civic duty leave</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Process</h3>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                                <div class="ml-4">
                                    <h4 class="font-medium text-gray-900">Employee Submits Request</h4>
                                    <p class="text-sm text-gray-600">Select dates, leave type, and provide reason</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                                <div class="ml-4">
                                    <h4 class="font-medium text-gray-900">Automatic Validation</h4>
                                    <p class="text-sm text-gray-600">System checks available balance and policy compliance</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                                <div class="ml-4">
                                    <h4 class="font-medium text-gray-900">Manager Review</h4>
                                    <p class="text-sm text-gray-600">Supervisor approves or denies with comments</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold">4</div>
                                <div class="ml-4">
                                    <h4 class="font-medium text-gray-900">Calendar Update</h4>
                                    <p class="text-sm text-gray-600">Approved time off appears on schedule and calendar</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reporting and Analytics -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Reporting and Analytics</h2>
                <div class="space-y-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Attendance Reports</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Standard Reports:</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Daily attendance summary</li>
                                    <li>• Weekly timesheet reports</li>
                                    <li>• Overtime analysis</li>
                                    <li>• Late arrival/early departure</li>
                                    <li>• Absence tracking</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Advanced Analytics:</h4>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li>• Attendance trends</li>
                                    <li>• Department comparisons</li>
                                    <li>• Productivity metrics</li>
                                    <li>• Seasonal patterns</li>
                                    <li>• Cost analysis</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Real-time Dashboard</h3>
                        <p class="text-gray-600 mb-4">Live view of attendance status across the organization.</p>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Dashboard Features:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                <ul class="space-y-1">
                                    <li>• Current clock-in status</li>
                                    <li>• Today's attendance rate</li>
                                    <li>• Scheduled vs actual hours</li>
                                    <li>• Overtime alerts</li>
                                </ul>
                                <ul class="space-y-1">
                                    <li>• Pending time-off requests</li>
                                    <li>• Late arrivals and absences</li>
                                    <li>• Department coverage levels</li>
                                    <li>• Schedule conflicts</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Integration and Compliance -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Integration and Compliance</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Third-party Integrations</h3>
                        <ul class="text-gray-600 space-y-2">
                            <li>• Payroll system sync</li>
                            <li>• Calendar applications</li>
                            <li>• Project management tools</li>
                            <li>• Security badge systems</li>
                            <li>• Accounting software</li>
                        </ul>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Labor Law Compliance</h3>
                        <ul class="text-gray-600 space-y-2">
                            <li>• Break time requirements</li>
                            <li>• Overtime regulations</li>
                            <li>• Minimum wage compliance</li>
                            <li>• Youth worker restrictions</li>
                            <li>• Union contract adherence</li>
                        </ul>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Audit Trail</h3>
                    <p class="text-gray-600 mb-4">Complete record of all attendance-related activities for compliance and auditing purposes.</p>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 mb-2">Tracked Activities:</h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• All clock in/out events with timestamps</li>
                            <li>• Time corrections and approvals</li>
                            <li>• Schedule changes and modifications</li>
                            <li>• Leave request submissions and approvals</li>
                            <li>• System administrator actions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

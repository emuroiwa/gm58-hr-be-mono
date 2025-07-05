<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    /**
     * Show main dashboard
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $companyId = $user->company_id;

        try {
            // Get dashboard statistics
            $stats = $this->reportService->generateDashboardStats($companyId);

            // Get recent activities (simplified for demo)
            $recentActivities = $this->getRecentActivities($companyId);

            // Get quick actions based on user role
            $quickActions = $this->getQuickActions($user);

            // Get notifications
            $notifications = $user->notifications()
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return view('dashboard.index', compact(
                'user', 
                'stats', 
                'recentActivities', 
                'quickActions', 
                'notifications'
            ));

        } catch (\Exception $e) {
            return view('dashboard.index', [
                'user' => $user,
                'stats' => [],
                'recentActivities' => [],
                'quickActions' => [],
                'notifications' => [],
                'error' => 'Failed to load dashboard data: ' . $e->getMessage()
            ]);
        }
    }

    private function getRecentActivities($companyId): array
    {
        // This would typically come from an activity log
        return [
            [
                'type' => 'employee_added',
                'message' => 'New employee John Doe was added to Marketing department',
                'time' => '2 hours ago',
                'icon' => 'user-plus',
                'color' => 'green'
            ],
            [
                'type' => 'leave_approved',
                'message' => 'Leave request approved for Jane Smith (Dec 25-29)',
                'time' => '4 hours ago',
                'icon' => 'calendar-check',
                'color' => 'blue'
            ],
            [
                'type' => 'payroll_processed',
                'message' => 'December payroll processed for 25 employees',
                'time' => '1 day ago',
                'icon' => 'credit-card',
                'color' => 'purple'
            ],
            [
                'type' => 'training_completed',
                'message' => 'Safety Training completed by 15 employees',
                'time' => '2 days ago',
                'icon' => 'book-open',
                'color' => 'orange'
            ]
        ];
    }

    private function getQuickActions($user): array
    {
        $actions = [];

        if (in_array($user->role, ['admin', 'hr'])) {
            $actions = [
                [
                    'title' => 'Add Employee',
                    'description' => 'Add a new employee to the system',
                    'url' => '/admin/employees/create',
                    'icon' => 'user-plus',
                    'color' => 'blue'
                ],
                [
                    'title' => 'Process Payroll',
                    'description' => 'Run monthly payroll processing',
                    'url' => '/admin/payroll/process',
                    'icon' => 'credit-card',
                    'color' => 'green'
                ],
                [
                    'title' => 'View Reports',
                    'description' => 'Access HR analytics and reports',
                    'url' => '/admin/reports',
                    'icon' => 'bar-chart',
                    'color' => 'purple'
                ],
                [
                    'title' => 'Manage Leaves',
                    'description' => 'Review pending leave requests',
                    'url' => '/admin/leaves',
                    'icon' => 'calendar',
                    'color' => 'orange'
                ]
            ];
        } elseif ($user->role === 'manager') {
            $actions = [
                [
                    'title' => 'Team Overview',
                    'description' => 'View your team\'s performance',
                    'url' => '/manager/team',
                    'icon' => 'users',
                    'color' => 'blue'
                ],
                [
                    'title' => 'Approve Leaves',
                    'description' => 'Review team leave requests',
                    'url' => '/manager/leaves',
                    'icon' => 'calendar-check',
                    'color' => 'green'
                ],
                [
                    'title' => 'Performance Reviews',
                    'description' => 'Conduct team evaluations',
                    'url' => '/manager/reviews',
                    'icon' => 'trending-up',
                    'color' => 'purple'
                ]
            ];
        } else {
            $actions = [
                [
                    'title' => 'Mark Attendance',
                    'description' => 'Clock in/out for today',
                    'url' => '/employee/attendance',
                    'icon' => 'clock',
                    'color' => 'blue'
                ],
                [
                    'title' => 'Apply for Leave',
                    'description' => 'Submit a new leave request',
                    'url' => '/employee/leaves/create',
                    'icon' => 'calendar-plus',
                    'color' => 'green'
                ],
                [
                    'title' => 'View Pay Slip',
                    'description' => 'Download latest pay slip',
                    'url' => '/employee/payslips',
                    'icon' => 'file-text',
                    'color' => 'purple'
                ],
                [
                    'title' => 'Update Profile',
                    'description' => 'Edit your personal information',
                    'url' => '/employee/profile',
                    'icon' => 'user',
                    'color' => 'orange'
                ]
            ];
        }

        return $actions;
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    /**
     * Get dashboard overview
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $stats = $this->reportService->generateDashboardStats($companyId);

            return response()->json([
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to load dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $stats = $this->reportService->generateDashboardStats($companyId);

            return response()->json([
                'data' => [
                    'employees' => $stats,
                    'recent_activities' => [], // Would implement activity tracking
                    'quick_actions' => $this->getQuickActions($request->user()),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to load statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getQuickActions($user): array
    {
        $actions = [];

        if (in_array($user->role, ['admin', 'hr'])) {
            $actions[] = ['label' => 'Add Employee', 'action' => 'create_employee'];
            $actions[] = ['label' => 'Process Payroll', 'action' => 'process_payroll'];
            $actions[] = ['label' => 'View Reports', 'action' => 'view_reports'];
        }

        if ($user->role === 'employee') {
            $actions[] = ['label' => 'Mark Attendance', 'action' => 'mark_attendance'];
            $actions[] = ['label' => 'Apply Leave', 'action' => 'apply_leave'];
            $actions[] = ['label' => 'View Pay Slip', 'action' => 'view_payslip'];
        }

        return $actions;
    }
}

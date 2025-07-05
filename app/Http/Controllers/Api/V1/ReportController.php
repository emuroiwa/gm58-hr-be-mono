<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Jobs\GenerateReport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    /**
     * Get dashboard report
     */
    public function getDashboardReport(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $stats = $this->reportService->generateDashboardStats($companyId);

            return response()->json([
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate dashboard report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get employee report
     */
    public function getEmployeeReport(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $filters = $request->only(['department_id', 'status', 'hire_date_from', 'hire_date_to']);

            $report = $this->reportService->generateEmployeeReport($companyId, $filters);

            return response()->json([
                'data' => $report
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate employee report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance report
     */
    public function getAttendanceReport(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        try {
            $companyId = $request->get('company_id');
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $report = $this->reportService->generateAttendanceReport($companyId, $startDate, $endDate);

            return response()->json([
                'data' => $report
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate attendance report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payroll report
     */
    public function getPayrollReport(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $payrollPeriodId = $request->get('payroll_period_id');

            $report = $this->reportService->generatePayrollReport($companyId, $payrollPeriodId);

            return response()->json([
                'data' => $report
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate payroll report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get leave report
     */
    public function getLeaveReport(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $filters = $request->only(['start_date', 'end_date', 'status', 'leave_type_id', 'employee_id']);

            $report = $this->reportService->generateLeaveReport($companyId, $filters);

            return response()->json([
                'data' => $report
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate leave report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get performance report
     */
    public function getPerformanceReport(Request $request): JsonResponse
    {
        try {
            $companyId = $request->get('company_id');
            $filters = $request->only(['year', 'department_id', 'rating_min', 'rating_max']);

            // This would need implementation in ReportService
            $report = [
                'message' => 'Performance report feature coming soon',
                'filters' => $filters
            ];

            return response()->json([
                'data' => $report
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate performance report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate report asynchronously
     */
    public function generateReport(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:employee,attendance,payroll,leave,performance',
            'format' => 'required|in:pdf,excel,csv',
            'filters' => 'sometimes|array'
        ]);

        try {
            $companyId = $request->get('company_id');
            $type = $request->get('type');
            $format = $request->get('format');
            $filters = $request->get('filters', []);

            // Dispatch background job for report generation
            GenerateReport::dispatch($companyId, $request->user()->id, $type, $filters, $format);

            return response()->json([
                'message' => 'Report generation started. You will be notified when complete.',
                'type' => $type,
                'format' => $format,
                'status' => 'processing'
            ], 202);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to start report generation',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get available downloads
     */
    public function getDownloads(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            
            // Get user's generated reports from storage
            $reports = \Illuminate\Support\Facades\Storage::files('reports');
            $userReports = array_filter($reports, function ($report) use ($userId) {
                return str_contains($report, "user_{$userId}_");
            });

            $downloads = array_map(function ($report) {
                $info = pathinfo($report);
                return [
                    'filename' => $info['basename'],
                    'type' => $this->getReportTypeFromFilename($info['basename']),
                    'format' => $info['extension'],
                    'size' => \Illuminate\Support\Facades\Storage::size($report),
                    'created_at' => \Illuminate\Support\Facades\Storage::lastModified($report),
                    'download_url' => route('reports.download', ['file' => $info['basename']])
                ];
            }, $userReports);

            return response()->json([
                'data' => $downloads
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve downloads',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download report file
     */
    public function downloadReport(Request $request, string $filename): mixed
    {
        try {
            $filePath = "reports/{$filename}";
            
            if (!\Illuminate\Support\Facades\Storage::exists($filePath)) {
                return response()->json(['message' => 'Report file not found'], 404);
            }

            // Verify user owns this report
            $userId = $request->user()->id;
            if (!str_contains($filename, "user_{$userId}_")) {
                return response()->json(['message' => 'Unauthorized access to report'], 403);
            }

            return \Illuminate\Support\Facades\Storage::download($filePath);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to download report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getReportTypeFromFilename(string $filename): string
    {
        if (str_contains($filename, 'employee')) return 'employee';
        if (str_contains($filename, 'attendance')) return 'attendance';
        if (str_contains($filename, 'payroll')) return 'payroll';
        if (str_contains($filename, 'leave')) return 'leave';
        if (str_contains($filename, 'performance')) return 'performance';
        return 'unknown';
    }
}

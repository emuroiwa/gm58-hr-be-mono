<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\ReportService;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class GenerateReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; // 30 minutes
    public $tries = 2;

    public function __construct(
        public int $companyId,
        public int $requestedBy,
        public string $reportType,
        public array $filters = [],
        public string $format = 'pdf'
    ) {}

    public function handle(ReportService $reportService, NotificationService $notificationService)
    {
        try {
            Log::info("Starting report generation", [
                'company_id' => $this->companyId,
                'report_type' => $this->reportType,
                'format' => $this->format
            ]);

            $reportData = $this->generateReportData($reportService);
            $filePath = $this->saveReport($reportData);

            // Notify user that report is ready
            $user = User::find($this->requestedBy);
            if ($user) {
                $notificationService->sendNotification(
                    $user->id,
                    'Report Generated Successfully',
                    "Your {$this->reportType} report has been generated and is ready for download.",
                    'success',
                    [
                        'report_type' => $this->reportType,
                        'file_path' => $filePath,
                        'download_url' => Storage::url($filePath)
                    ]
                );
            }

            Log::info("Report generation completed", [
                'file_path' => $filePath,
                'report_type' => $this->reportType
            ]);

        } catch (Exception $e) {
            Log::error("Report generation failed", [
                'company_id' => $this->companyId,
                'report_type' => $this->reportType,
                'error' => $e->getMessage()
            ]);

            // Notify user of failure
            $user = User::find($this->requestedBy);
            if ($user) {
                $notificationService = app(NotificationService::class);
                $notificationService->sendNotification(
                    $user->id,
                    'Report Generation Failed',
                    "Failed to generate {$this->reportType} report: " . $e->getMessage(),
                    'error'
                );
            }

            throw $e;
        }
    }

    private function generateReportData(ReportService $reportService): array
    {
        switch ($this->reportType) {
            case 'employee':
                return $reportService->generateEmployeeReport($this->companyId, $this->filters);

            case 'attendance':
                return $reportService->generateAttendanceReport(
                    $this->companyId,
                    $this->filters['start_date'] ?? now()->startOfMonth(),
                    $this->filters['end_date'] ?? now()->endOfMonth()
                );

            case 'payroll':
                return $reportService->generatePayrollReport(
                    $this->companyId,
                    $this->filters['payroll_period_id'] ?? null
                );

            case 'leave':
                return $reportService->generateLeaveReport($this->companyId, $this->filters);

            default:
                throw new Exception("Unknown report type: {$this->reportType}");
        }
    }

    private function saveReport(array $data): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "{$this->reportType}_report_{$this->companyId}_{$timestamp}.{$this->format}";
        $filePath = "reports/{$filename}";

        switch ($this->format) {
            case 'pdf':
                $pdf = app('dompdf.wrapper');
                $pdf->loadView('reports.pdf.' . $this->reportType, $data);
                Storage::put($filePath, $pdf->output());
                break;

            case 'excel':
                // You would use Laravel Excel here
                // Excel::store(new ReportExport($data), $filePath);
                break;

            case 'csv':
                $csv = $this->arrayToCsv($data);
                Storage::put($filePath, $csv);
                break;

            default:
                // JSON format
                Storage::put($filePath, json_encode($data, JSON_PRETTY_PRINT));
                break;
        }

        return $filePath;
    }

    private function arrayToCsv(array $data): string
    {
        $output = fopen('php://temp', 'r+');
        
        if (isset($data[0]) && is_array($data[0])) {
            // Write headers
            fputcsv($output, array_keys($data[0]));
            
            // Write data
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }

    public function failed(Exception $exception)
    {
        Log::error("Report generation job permanently failed", [
            'company_id' => $this->companyId,
            'report_type' => $this->reportType,
            'error' => $exception->getMessage()
        ]);
    }
}

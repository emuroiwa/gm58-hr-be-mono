<?php

namespace App\Jobs;

use App\Models\PayrollPeriod;
use App\Services\PayrollService;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class ProcessPayroll implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;

    public function __construct(
        public PayrollPeriod $payrollPeriod
    ) {}

    public function handle(PayrollService $payrollService, NotificationService $notificationService)
    {
        try {
            Log::info("Starting payroll processing for period: {$this->payrollPeriod->id}");

            // Process the payroll
            $processedPeriod = $payrollService->calculatePayroll($this->payrollPeriod);

            // Send notifications to HR and admins
            $this->notifyStakeholders($notificationService, $processedPeriod);

            Log::info("Payroll processing completed for period: {$this->payrollPeriod->id}");

        } catch (Exception $e) {
            Log::error("Payroll processing failed for period: {$this->payrollPeriod->id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update payroll period status to failed
            $this->payrollPeriod->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            // Notify admins of failure
            $this->notifyFailure($notificationService, $e);

            throw $e;
        }
    }

    public function failed(Exception $exception)
    {
        Log::error("Payroll job permanently failed for period: {$this->payrollPeriod->id}", [
            'error' => $exception->getMessage()
        ]);

        $this->payrollPeriod->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage()
        ]);
    }

    private function notifyStakeholders(NotificationService $notificationService, PayrollPeriod $period)
    {
        // Find HR users and admins in the company
        $users = \App\Models\User::where('company_id', $period->company_id)
            ->whereIn('role', ['admin', 'hr'])
            ->get();

        foreach ($users as $user) {
            $notificationService->sendNotification(
                $user->id,
                'Payroll Processed Successfully',
                "Payroll for period '{$period->name}' has been processed successfully. Total employees: " . $period->payrolls()->count(),
                'success',
                ['payroll_period_id' => $period->id]
            );
        }
    }

    private function notifyFailure(NotificationService $notificationService, Exception $e)
    {
        $users = \App\Models\User::where('company_id', $this->payrollPeriod->company_id)
            ->whereIn('role', ['admin', 'hr'])
            ->get();

        foreach ($users as $user) {
            $notificationService->sendNotification(
                $user->id,
                'Payroll Processing Failed',
                "Payroll processing failed for period '{$this->payrollPeriod->name}'. Error: " . $e->getMessage(),
                'error',
                ['payroll_period_id' => $this->payrollPeriod->id]
            );
        }
    }
}

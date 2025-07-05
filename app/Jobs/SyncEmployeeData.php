<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class SyncEmployeeData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes

    public function __construct(
        public int $companyId,
        public array $syncOptions = []
    ) {}

    public function handle(EmployeeService $employeeService)
    {
        try {
            Log::info("Starting employee data sync for company: {$this->companyId}");

            $employees = Employee::where('company_id', $this->companyId)->get();
            $synced = 0;

            foreach ($employees as $employee) {
                $this->syncEmployeeRecord($employee);
                $synced++;
            }

            Log::info("Employee data sync completed", [
                'company_id' => $this->companyId,
                'employees_synced' => $synced
            ]);

        } catch (Exception $e) {
            Log::error("Employee data sync failed", [
                'company_id' => $this->companyId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    private function syncEmployeeRecord(Employee $employee)
    {
        // Sync user account status with employee status
        if ($employee->user) {
            $isActive = $employee->status === 'active';
            if ($employee->user->is_active !== $isActive) {
                $employee->user->update(['is_active' => $isActive]);
            }
        }

        // Update calculated fields
        $employee->update([
            'full_name' => $employee->first_name . ' ' . $employee->last_name,
            'years_of_service' => $employee->hire_date ? 
                now()->diffInYears($employee->hire_date) : 0
        ]);
    }
}

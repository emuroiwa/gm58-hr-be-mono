<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Services\AttendanceService;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class CalculateAttendance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 900; // 15 minutes
    public $tries = 2;

    public function __construct(
        public int $companyId,
        public string $startDate,
        public string $endDate,
        public ?int $employeeId = null
    ) {}

    public function handle(AttendanceService $attendanceService, NotificationService $notificationService)
    {
        try {
            Log::info("Starting attendance calculation", [
                'company_id' => $this->companyId,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'employee_id' => $this->employeeId
            ]);

            $results = $this->calculateAttendanceData($attendanceService);

            // Notify admins of completion
            $this->notifyCompletion($notificationService, $results);

            Log::info("Attendance calculation completed", $results);

        } catch (Exception $e) {
            Log::error("Attendance calculation failed", [
                'company_id' => $this->companyId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    private function calculateAttendanceData(AttendanceService $attendanceService): array
    {
        $query = Employee::where('company_id', $this->companyId)->where('status', 'active');
        
        if ($this->employeeId) {
            $query->where('id', $this->employeeId);
        }

        $employees = $query->get();
        $processed = 0;
        $totalWorkingDays = $this->getWorkingDays();
        $summaryData = [];

        foreach ($employees as $employee) {
            $attendanceData = $this->calculateEmployeeAttendance($employee, $attendanceService);
            $summaryData[] = $attendanceData;
            $processed++;
        }

        return [
            'employees_processed' => $processed,
            'total_working_days' => $totalWorkingDays,
            'period' => [
                'start_date' => $this->startDate,
                'end_date' => $this->endDate
            ],
            'summary' => $summaryData
        ];
    }

    private function calculateEmployeeAttendance(Employee $employee, AttendanceService $attendanceService): array
    {
        $attendances = $attendanceService->getEmployeeAttendance($employee->id, [
            'date_from' => $this->startDate,
            'date_to' => $this->endDate
        ]);

        $totalDays = $attendances->count();
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays = $attendances->where('status', 'absent')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $totalHours = $attendances->sum('worked_hours');
        $averageHours = $totalDays > 0 ? $totalHours / $totalDays : 0;

        // Calculate attendance percentage
        $workingDays = $this->getWorkingDays();
        $attendancePercentage = $workingDays > 0 ? ($presentDays / $workingDays) * 100 : 0;

        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->first_name . ' ' . $employee->last_name,
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'total_hours' => $totalHours,
            'average_hours' => round($averageHours, 2),
            'attendance_percentage' => round($attendancePercentage, 2),
            'working_days' => $workingDays
        ];
    }

    private function getWorkingDays(): int
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($this->endDate);
        $workingDays = 0;

        while ($start->lte($end)) {
            if ($start->isWeekday()) {
                $workingDays++;
            }
            $start->addDay();
        }

        return $workingDays;
    }

    private function notifyCompletion(NotificationService $notificationService, array $results)
    {
        // Find HR and admin users
        $users = \App\Models\User::where('company_id', $this->companyId)
            ->whereIn('role', ['admin', 'hr'])
            ->get();

        foreach ($users as $user) {
            $notificationService->sendNotification(
                $user->id,
                'Attendance Calculation Completed',
                "Attendance calculation completed for {$results['employees_processed']} employees from {$this->startDate} to {$this->endDate}",
                'info',
                $results
            );
        }
    }

    public function failed(Exception $exception)
    {
        Log::error("Attendance calculation job permanently failed", [
            'company_id' => $this->companyId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'error' => $exception->getMessage()
        ]);
    }
}

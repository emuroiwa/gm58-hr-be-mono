<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\Attendance;
use App\Jobs\SendEmailNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAttendanceReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes

    public function __construct(
        public int $companyId
    ) {}

    public function handle()
    {
        try {
            Log::info("Sending attendance reminders for company: {$this->companyId}");

            $employees = $this->getEmployeesNeedingReminders();
            $remindersSent = 0;

            foreach ($employees as $employee) {
                if ($employee->email) {
                    SendEmailNotification::dispatch(
                        $employee->email,
                        'Attendance Reminder',
                        'Please remember to mark your attendance for today.',
                        [
                            'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                            'date' => now()->format('Y-m-d')
                        ],
                        'attendance_reminder',
                        $employee->user?->id
                    );
                    $remindersSent++;
                }
            }

            Log::info("Attendance reminders sent", [
                'company_id' => $this->companyId,
                'reminders_sent' => $remindersSent
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to send attendance reminders", [
                'company_id' => $this->companyId,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getEmployeesNeedingReminders()
    {
        $today = now()->format('Y-m-d');
        
        return Employee::where('company_id', $this->companyId)
            ->where('status', 'active')
            ->whereDoesntHave('attendances', function ($query) use ($today) {
                $query->where('date', $today);
            })
            ->with('user')
            ->get();
    }
}

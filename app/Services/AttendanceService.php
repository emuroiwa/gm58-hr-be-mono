<?php

namespace App\Services;

use App\Contracts\AttendanceRepositoryInterface;
use App\Contracts\EmployeeRepositoryInterface;
use Carbon\Carbon;
use App\Events\AttendanceMarked;

class AttendanceService
{
    public function __construct(
        private AttendanceRepositoryInterface $attendanceRepository,
        private EmployeeRepositoryInterface $employeeRepository
    ) {}

    public function markAttendance($employeeId, array $data)
    {
        $date = $data['date'] ?? now()->format('Y-m-d');
        
        // Check if attendance already marked for this date
        $existingAttendance = $this->attendanceRepository->findByEmployeeAndDate($employeeId, $date);

        if ($existingAttendance) {
            throw new \Exception('Attendance already marked for this date');
        }

        $attendance = $this->attendanceRepository->create([
            'employee_id' => $employeeId,
            'date' => $date,
            'check_in' => $data['check_in'] ?? now(),
            'status' => $data['status'] ?? 'present',
            'notes' => $data['notes'] ?? null,
        ]);

        event(new AttendanceMarked($attendance));

        return $attendance;
    }

    public function markCheckOut($employeeId, $checkOutTime = null)
    {
        $attendance = $this->attendanceRepository->findTodayAttendance($employeeId);

        if (!$attendance) {
            throw new \Exception('No check-in record found for today');
        }

        $checkOut = $checkOutTime ?? now();
        $updatedAttendance = $this->attendanceRepository->update($attendance->id, ['check_out' => $checkOut]);

        // Calculate worked hours
        $this->calculateWorkedHours($updatedAttendance);

        return $updatedAttendance;
    }

    public function getAttendanceReport($companyId, array $filters = [])
    {
        return $this->attendanceRepository->getReportWithFilters($companyId, $filters);
    }

    public function getEmployeeAttendance($employeeId, array $filters = [])
    {
        return $this->attendanceRepository->getEmployeeAttendance($employeeId, $filters);
    }

    public function updateAttendance($attendanceId, $companyId, array $data)
    {
        $attendance = $this->attendanceRepository->findByIdAndCompany($attendanceId, $companyId);
        
        if (!$attendance) {
            throw new \Exception('Attendance record not found');
        }

        $updatedAttendance = $this->attendanceRepository->update($attendanceId, $data);

        if ($updatedAttendance->check_in && $updatedAttendance->check_out) {
            $this->calculateWorkedHours($updatedAttendance);
        }

        return $updatedAttendance;
    }

    private function calculateWorkedHours($attendance)
    {
        if ($attendance->check_in && $attendance->check_out) {
            $checkIn = Carbon::parse($attendance->check_in);
            $checkOut = Carbon::parse($attendance->check_out);
            $workedHours = $checkOut->diffInHours($checkIn);
            
            $this->attendanceRepository->update($attendance->id, ['worked_hours' => $workedHours]);
        }
    }
}

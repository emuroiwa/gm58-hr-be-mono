<?php

namespace App\Services;

use App\Contracts\EmployeeRepositoryInterface;
use App\Contracts\AttendanceRepositoryInterface;
use App\Contracts\LeaveRepositoryInterface;
use App\Contracts\PayrollRepositoryInterface;

class ReportService
{
    public function __construct(
        private EmployeeRepositoryInterface $employeeRepository,
        private AttendanceRepositoryInterface $attendanceRepository,
        private LeaveRepositoryInterface $leaveRepository,
        private PayrollRepositoryInterface $payrollRepository
    ) {}

    public function generateEmployeeReport($companyId, array $filters = [])
    {
        $employees = $this->employeeRepository->getByCompanyWithFilters($companyId, $filters);

        return [
            'total_employees' => $this->employeeRepository->countByCompany($companyId),
            'active_employees' => $this->employeeRepository->countActiveByCompany($companyId),
            'inactive_employees' => $this->employeeRepository->countInactiveByCompany($companyId),
            'employees_by_department' => $this->employeeRepository->getEmployeesByDepartment($companyId),
            'employees' => $employees,
        ];
    }

    public function generateAttendanceReport($companyId, $startDate, $endDate)
    {
        $attendances = $this->attendanceRepository->getReportData($companyId, $startDate, $endDate);

        return [
            'total_records' => $attendances->count(),
            'present_days' => $attendances->where('status', 'present')->count(),
            'absent_days' => $attendances->where('status', 'absent')->count(),
            'late_arrivals' => $attendances->where('status', 'late')->count(),
            'average_hours' => $attendances->avg('worked_hours'),
            'attendances' => $attendances,
        ];
    }

    public function generatePayrollReport($companyId, $payrollPeriodId = null)
    {
        if ($payrollPeriodId) {
            return $this->payrollRepository->getPayrollDetailsByPeriod($payrollPeriodId);
        }

        return $this->payrollRepository->getPayrollSummaryByCompany($companyId);
    }

    public function generateLeaveReport($companyId, array $filters = [])
    {
        return $this->leaveRepository->getLeaveReport($companyId, $filters);
    }

    public function generateDashboardStats($companyId)
    {
        return [
            'total_employees' => $this->employeeRepository->countByCompany($companyId),
            'present_today' => $this->attendanceRepository->countPresentToday($companyId),
            'on_leave_today' => $this->leaveRepository->countOnLeaveToday($companyId),
            'pending_leaves' => $this->leaveRepository->countPendingLeaves($companyId),
            'recent_hires' => $this->employeeRepository->getRecentHires($companyId, 30),
            'upcoming_birthdays' => $this->employeeRepository->getUpcomingBirthdays($companyId, 7),
        ];
    }
}

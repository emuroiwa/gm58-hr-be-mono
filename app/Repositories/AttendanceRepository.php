<?php

namespace App\Repositories;

use App\Contracts\AttendanceRepositoryInterface;
use App\Models\Attendance;
use App\Models\TimeEntry;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function getAttendanceByEmployee(int $employeeId, Carbon $startDate, Carbon $endDate): Collection
    {
        return Attendance::where('employee_id', $employeeId)
                        ->whereBetween('date', [$startDate, $endDate])
                        ->orderBy('date', 'desc')
                        ->get();
    }

    public function recordAttendance(array $data): Attendance
    {
        return Attendance::create($data);
    }

    public function updateAttendance(int $id, array $data): bool
    {
        return Attendance::where('id', $id)->update($data);
    }

    public function deleteAttendance(int $id): bool
    {
        return Attendance::where('id', $id)->delete();
    }

    public function getAttendanceByCompany(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Attendance::whereHas('employee', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with('employee');

        if (isset($filters['date_from'])) {
            $query->whereDate('date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('date', '<=', $filters['date_to']);
        }

        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        return $query->orderBy('date', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function createTimeEntry(array $data): TimeEntry
    {
        return TimeEntry::create($data);
    }

    public function getTimeEntries(int $employeeId, Carbon $date): Collection
    {
        return TimeEntry::where('employee_id', $employeeId)
                       ->whereDate('date', $date)
                       ->orderBy('time', 'asc')
                       ->get();
    }

    public function calculateHoursWorked(int $employeeId, Carbon $startDate, Carbon $endDate): float
    {
        return Attendance::where('employee_id', $employeeId)
                        ->whereBetween('date', [$startDate, $endDate])
                        ->sum('hours_worked');
    }

    public function getAttendanceReport(int $companyId, Carbon $startDate, Carbon $endDate): Collection
    {
        return Attendance::whereHas('employee', function ($q) use ($companyId) {
                         $q->where('company_id', $companyId);
                     })
                     ->whereBetween('date', [$startDate, $endDate])
                     ->with(['employee.department', 'employee.position'])
                     ->get()
                     ->groupBy('employee_id');
    }
}

<?php
// app/Repositories/AttendanceRepository.php

namespace App\Repositories;

use App\Models\Attendance;
use App\Contracts\AttendanceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function create(array $data): Attendance
    {
        return Attendance::create($data);
    }

    public function find(string $id): ?Attendance
    {
        return Attendance::find($id);
    }

    public function update(string $id, array $data): Attendance
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update($data);
        return $attendance->fresh();
    }

    public function getTodayAttendance(string $employeeId): ?Attendance
    {
        return Attendance::where('employee_id', $employeeId)
            ->whereDate('date', Carbon::today())
            ->first();
    }

    public function getByEmployee(string $employeeId, array $filters = []): LengthAwarePaginator
    {
        $query = Attendance::where('employee_id', $employeeId);

        if (isset($filters['start_date'])) {
            $query->whereDate('date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('date', '<=', $filters['end_date']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('date', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function getByCompany(string $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Attendance::whereHas('employee', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with('employee');

        if (isset($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }

        if (isset($filters['start_date'])) {
            $query->whereDate('date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('date', '<=', $filters['end_date']);
        }

        if (isset($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('date', 'desc')
                    ->orderBy('check_in', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function countPresentToday(string $companyId): int
    {
        return Attendance::whereHas('employee', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })
        ->whereDate('date', Carbon::today())
        ->where('status', 'present')
        ->count();
    }

    public function getReportData(string $companyId, string $startDate, string $endDate): Collection
    {
        return Attendance::whereHas('employee', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })
        ->whereBetween('date', [$startDate, $endDate])
        ->with('employee')
        ->get();
    }
}
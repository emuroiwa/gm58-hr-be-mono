<?php
// app/Repositories/LeaveRepository.php

namespace App\Repositories;

use App\Models\Leave;
use App\Contracts\LeaveRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class LeaveRepository implements LeaveRepositoryInterface
{
    public function create(array $data): Leave
    {
        return Leave::create($data);
    }

    public function find(string $id): ?Leave
    {
        return Leave::with(['employee', 'leaveType', 'approvedBy'])->find($id);
    }

    public function update(string $id, array $data): Leave
    {
        $leave = Leave::findOrFail($id);
        $leave->update($data);
        return $leave->fresh(['employee', 'leaveType', 'approvedBy']);
    }

    public function getByEmployee(string $employeeId, array $filters = []): LengthAwarePaginator
    {
        $query = Leave::where('employee_id', $employeeId)
            ->with(['leaveType', 'approvedBy']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['leave_type_id'])) {
            $query->where('leave_type_id', $filters['leave_type_id']);
        }

        if (isset($filters['year'])) {
            $query->whereYear('start_date', $filters['year']);
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function getByCompany(string $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Leave::whereHas('employee', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })->with(['employee', 'leaveType', 'approvedBy']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        if (isset($filters['start_date'])) {
            $query->whereDate('start_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('end_date', '<=', $filters['end_date']);
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function countPendingLeaves(string $companyId): int
    {
        return Leave::whereHas('employee', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })
        ->where('status', 'pending')
        ->count();
    }

    public function countOnLeaveToday(string $companyId): int
    {
        $today = Carbon::today();
        
        return Leave::whereHas('employee', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })
        ->where('status', 'approved')
        ->whereDate('start_date', '<=', $today)
        ->whereDate('end_date', '>=', $today)
        ->count();
    }

    public function getLeaveReport(string $companyId, array $filters = []): array
    {
        $query = Leave::whereHas('employee', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('start_date', [$filters['start_date'], $filters['end_date']]);
        }

        $leaves = $query->with(['employee', 'leaveType'])->get();

        return [
            'total_leaves' => $leaves->count(),
            'approved' => $leaves->where('status', 'approved')->count(),
            'pending' => $leaves->where('status', 'pending')->count(),
            'rejected' => $leaves->where('status', 'rejected')->count(),
            'by_type' => $leaves->groupBy('leave_type_id')->map->count(),
            'by_department' => $leaves->groupBy('employee.department_id')->map->count(),
            'total_days' => $leaves->sum('days'),
        ];
    }
}
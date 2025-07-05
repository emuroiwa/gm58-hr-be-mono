<?php

namespace App\Repositories;

use App\Contracts\LeaveRepositoryInterface;
use App\Models\Leave;
use App\Models\LeaveType;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class LeaveRepository implements LeaveRepositoryInterface
{
    public function getAllLeaves(int $companyId, array $filters = []): LengthAwarePaginator
    {
        $query = Leave::whereHas('employee', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }
        
        if (isset($filters['leave_type_id'])) {
            $query->where('leave_type_id', $filters['leave_type_id']);
        }
        
        return $query->with(['employee', 'leaveType', 'approvedBy'])
                    ->orderBy('start_date', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function createLeave(array $data): Leave
    {
        return Leave::create($data);
    }

    public function findLeave(int $id, int $companyId): ?Leave
    {
        return Leave::whereHas('employee', function ($q) use ($companyId) {
                   $q->where('company_id', $companyId);
               })
               ->where('id', $id)
               ->with(['employee', 'leaveType', 'approvedBy'])
               ->first();
    }

    public function updateLeave(int $id, array $data, int $companyId): bool
    {
        return Leave::whereHas('employee', function ($q) use ($companyId) {
                   $q->where('company_id', $companyId);
               })
               ->where('id', $id)
               ->update($data);
    }

    public function deleteLeave(int $id, int $companyId): bool
    {
        return Leave::whereHas('employee', function ($q) use ($companyId) {
                   $q->where('company_id', $companyId);
               })
               ->where('id', $id)
               ->delete();
    }

    public function getEmployeeLeaves(int $employeeId, int $companyId): Collection
    {
        return Leave::where('employee_id', $employeeId)
                   ->whereHas('employee', function ($q) use ($companyId) {
                       $q->where('company_id', $companyId);
                   })
                   ->with(['leaveType', 'approvedBy'])
                   ->orderBy('start_date', 'desc')
                   ->get();
    }

    public function getPendingLeaves(int $companyId): Collection
    {
        return Leave::whereHas('employee', function ($q) use ($companyId) {
                   $q->where('company_id', $companyId);
               })
               ->where('status', 'pending')
               ->with(['employee', 'leaveType'])
               ->orderBy('created_at', 'asc')
               ->get();
    }

    public function approveLeave(int $id, int $companyId): bool
    {
        return $this->updateLeave($id, [
            'status' => 'approved',
            'approved_at' => now(),
        ], $companyId);
    }

    public function rejectLeave(int $id, int $companyId, string $reason = ''): bool
    {
        return $this->updateLeave($id, [
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_at' => now(),
        ], $companyId);
    }

    public function getLeaveBalance(int $employeeId, int $leaveTypeId): float
    {
        $currentYear = Carbon::now()->year;
        
        $usedLeaves = Leave::where('employee_id', $employeeId)
                          ->where('leave_type_id', $leaveTypeId)
                          ->where('status', 'approved')
                          ->whereYear('start_date', $currentYear)
                          ->sum('days');
        
        $leaveType = LeaveType::find($leaveTypeId);
        $allocatedDays = $leaveType ? $leaveType->days_per_year : 0;
        
        return max(0, $allocatedDays - $usedLeaves);
    }

    public function getLeaveTypes(int $companyId): Collection
    {
        return LeaveType::where('company_id', $companyId)
                       ->where('is_active', true)
                       ->orderBy('name')
                       ->get();
    }

    public function createLeaveType(array $data): LeaveType
    {
        return LeaveType::create($data);
    }
}

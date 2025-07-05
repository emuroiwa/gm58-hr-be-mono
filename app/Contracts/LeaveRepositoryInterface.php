<?php

namespace App\Contracts;

use App\Models\Leave;
use App\Models\LeaveType;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

interface LeaveRepositoryInterface
{
    public function getAllLeaves(int $companyId, array $filters = []): LengthAwarePaginator;
    public function createLeave(array $data): Leave;
    public function findLeave(int $id, int $companyId): ?Leave;
    public function updateLeave(int $id, array $data, int $companyId): bool;
    public function deleteLeave(int $id, int $companyId): bool;
    public function getEmployeeLeaves(int $employeeId, int $companyId): Collection;
    public function getPendingLeaves(int $companyId): Collection;
    public function approveLeave(int $id, int $companyId): bool;
    public function rejectLeave(int $id, int $companyId, string $reason = ''): bool;
    public function getLeaveBalance(int $employeeId, int $leaveTypeId): float;
    public function getLeaveTypes(int $companyId): Collection;
    public function createLeaveType(array $data): LeaveType;
}
